<?php

namespace Tests\Integration;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentWorkflowTest extends TestCase
{
    protected User $student;
    protected User $otherStudent;
    protected User $department;
    protected User $otherDepartment;
    protected User $kln;
    protected int $studentId;
    protected int $otherStudentId;

    protected function setUp(): void
    {
        parent::setUp();
        // Use an isolated test database; never touch the project's .env database.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'session.driver' => 'array', 'cache.default' => 'array']);
        DB::purge('sqlite');
        foreach (glob(database_path('migrations/*.php')) as $migration) {
            // This pre-existing migration contains PostgreSQL-only ALTER CONSTRAINT for
            // the unrelated audit log. All document, notification and user migrations run.
            if (str_contains($migration, 'alter_log_table_polymorphic')) {
                continue;
            }
            (require $migration)->up();
        }
        Storage::fake('local');
        Storage::fake('public');
        $first = DB::table('jurusan')->insertGetId(['namaJurusan' => 'Sistem Informasi', 'created_at' => now(), 'updated_at' => now()]);
        $second = DB::table('jurusan')->insertGetId(['namaJurusan' => 'Informatika', 'created_at' => now(), 'updated_at' => now()]);
        $this->department = $this->user('jurusan', $first);
        $this->otherDepartment = $this->user('jurusan', $second);
        $this->student = $this->user('mahasiswa', $first);
        $this->otherStudent = $this->user('mahasiswa', $second);
        $this->kln = $this->user('kln', null);
        $this->studentId = $this->studentRecord($this->student);
        $this->otherStudentId = $this->studentRecord($this->otherStudent);
    }

    private function user(string $role, ?int $jurusan): User
    {
        return User::create(['email' => $role.uniqid().'@example.test', 'password' => 'password', 'role' => $role,
            'jurusan_id' => $jurusan, 'profile_completed' => true, 'status' => 'active']);
    }

    private function studentRecord(User $user): int
    {
        return DB::table('mahasiswa')->insertGetId([
            'nama' => 'Mahasiswa '.$user->id, 'npm' => '123'.$user->id, 'noWa' => '08123'.$user->id,
            'tglLahir' => '2002-01-01', 'warNeg' => 'Indonesia', 'alamatAsal' => 'Depok', 'alamatIndo' => 'Depok',
            'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function pdf(string $name = 'dokumen.pdf'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF");
    }

    private function academic(array $changes = []): array
    {
        return array_replace([
            'mahasiswa_id' => $this->studentId, 'tipeDkmn' => 'KRS', 'namaDkmn' => 'KRS Ganjil 2026/2027',
            'tglTerbit' => '2026-10-01', 'tglKdlwrs' => '2027-02-01', 'file' => $this->pdf(),
        ], $changes);
    }

    private function newRequest(): int
    {
        $this->actingAs($this->student)->post(route('mahasiswa.request.store'), ['tipeDkmn' => 'Surat_Keterangan', 'message' => 'Keperluan administrasi kampus.'])
            ->assertRedirect(route('mahasiswa.request.index'))->assertSessionHasNoErrors();
        return DB::table('reqDokumen')->max('id');
    }

    public function test_academic_uploads_all_four_types_and_downloads_are_scoped(): void
    {
        foreach (['KRS', 'FRS', 'Daftar_Nilai', 'Absensi'] as $type) {
            $this->actingAs($this->department)->post(route('jurusan.dokumen.store'), $this->academic(['tipeDkmn' => $type]))
                ->assertRedirect(route('jurusan.dokumen.index'))->assertSessionHasNoErrors();
            $id = DB::table('dokumen')->max('id');
            $this->assertDatabaseHas('dokumen', ['id' => $id, 'tipeDkmn' => $type, 'penerbit' => 'UNIVERSITAS', 'status' => 'approved']);
            $file = DB::table('fileDetail')->where('dokumen_id', $id)->first();
            Storage::disk('local')->assertExists($file->path);
            $this->actingAs($this->student)->get(route('mahasiswa.dokumen.download', $id))->assertOk();
            $this->actingAs($this->otherStudent)->get(route('mahasiswa.dokumen.download', $id))->assertForbidden();
            $this->actingAs($this->otherDepartment)->get(route('jurusan.dokumen.file', $id))->assertForbidden();
        }
        $this->assertDatabaseCount('notification_mahasiswa', 4);
        $this->assertDatabaseCount('historyDokumen', 4);
    }

    public function test_cross_department_writes_invalid_files_and_wrong_roles_are_blocked(): void
    {
        $this->actingAs($this->department)->post(route('jurusan.dokumen.store'), $this->academic(['mahasiswa_id' => $this->otherStudentId]))->assertForbidden();
        $this->actingAs($this->student)->post(route('jurusan.dokumen.store'), $this->academic())->assertForbidden();
        $this->actingAs($this->department)->post(route('jurusan.dokumen.store'), $this->academic(['file' => UploadedFile::fake()->createWithContent('evil.php', '<?php echo "bad";')]))->assertSessionHasErrors('file');
        $this->post(route('jurusan.dokumen.store'), $this->academic(['file' => UploadedFile::fake()->create('large.pdf', 10241, 'application/pdf')]))->assertSessionHasErrors('file');
        $this->post(route('jurusan.dokumen.store'), $this->academic(['tglKdlwrs' => '2025-01-01']))->assertSessionHasErrors('tglKdlwrs');
        $this->assertDatabaseCount('dokumen', 0);
        $this->assertDatabaseCount('fileDetail', 0);
        $unassigned = $this->user('jurusan', null);
        $this->actingAs($unassigned)->get(route('jurusan.dokumen.create'))->assertForbidden();
    }

    public function test_department_can_replace_and_archive_but_students_cannot_rewrite_official_files(): void
    {
        $this->actingAs($this->department)->post(route('jurusan.dokumen.store'), $this->academic())->assertSessionHasNoErrors();
        $id = DB::table('dokumen')->max('id');
        $this->actingAs($this->otherDepartment)->get(route('jurusan.dokumen.edit', $id))->assertNotFound();
        $this->actingAs($this->student)->get(route('mahasiswa.dokumen.edit', $id))->assertForbidden();
        $this->delete(route('mahasiswa.dokumen.destroy', $id))->assertForbidden();
        $this->patch(route('mahasiswa.dokumen.update', $id), $this->academic(['penerbit' => 'KLN', 'noDkmn' => 'fake']))->assertForbidden();
        $this->actingAs($this->department)->patch(route('jurusan.dokumen.update', $id), $this->academic(['namaDkmn' => 'KRS revisi']))->assertSessionHasNoErrors();
        $latest = DB::table('fileDetail')->where('dokumen_id', $id)->orderByDesc('id')->first();
        $this->actingAs($this->student)->get(route('mahasiswa.dokumen.download', $id))->assertDownload(basename($latest->path));
        $this->actingAs($this->department)->delete(route('jurusan.dokumen.destroy', $id))->assertRedirect();
        $this->actingAs($this->student)->get(route('mahasiswa.dokumen.download', $id))->assertForbidden();
    }

    public function test_request_to_kln_round_trip_rejection_resubmission_and_latest_download(): void
    {
        $id = $this->newRequest();
        $this->assertDatabaseHas('notification_users', ['user_id' => $this->kln->id]);
        $this->actingAs($this->kln)->get(route('kln.dokumen.show', $id))->assertOk()->assertJsonPath('status', 'pending');
        $this->postJson(route('kln.dokumen.reject', $id), ['keterangan' => 'Lengkapi keperluan.'])->assertOk();
        $this->actingAs($this->student)->get(route('mahasiswa.request.index'))->assertOk()->assertSee('Lengkapi keperluan.');
        $this->get(route('mahasiswa.request.file', $id))->assertNotFound();
        $this->actingAs($this->kln)->post(route('kln.dokumen.upload', $id), ['file' => $this->pdf()])->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('reqDokumen', ['id' => $id, 'status' => 'approved', 'keterangan' => null]);
        $this->post(route('kln.dokumen.upload', $id), ['file' => $this->pdf('revisi.pdf')])->assertOk();
        $latest = DB::table('fileDetail')->where('reqDokumen_id', $id)->orderByDesc('id')->first();
        $this->actingAs($this->student)->get(route('mahasiswa.request.file', $id))->assertDownload(basename($latest->path));
        $this->actingAs($this->otherStudent)->get(route('mahasiswa.request.file', $id))->assertNotFound();
        $this->post(route('kln.dokumen.upload', $id), ['file' => $this->pdf()])->assertForbidden();
        $this->assertDatabaseCount('notification_mahasiswa', 3);
    }

    public function test_request_validation_and_legacy_endpoint_use_existing_table(): void
    {
        $this->actingAs($this->student)->post(route('mahasiswa.request.store'), ['tipeDkmn' => 'UNKNOWN', 'message' => 'abc'])->assertSessionHasErrors('tipeDkmn');
        $this->post(route('mahasiswa.request.store'), ['tipeDkmn' => 'KRS', 'message' => str_repeat('x', 256)])->assertSessionHasErrors('message');
        $this->post(route('mahasiswa.request.quick'), ['jenis_dokumen' => 'KRS', 'req_bagian' => 'kln', 'deskripsi' => 'Kebutuhan registrasi'])->assertRedirect(route('mahasiswa.request.index'));
        $this->assertDatabaseCount('reqDokumen', 1);
        $id = DB::table('reqDokumen')->max('id');
        $this->actingAs($this->kln)->postJson(route('kln.dokumen.reject', $id), ['keterangan' => ''])->assertUnprocessable();
        $this->postJson(route('kln.dokumen.upload', $id), ['file' => UploadedFile::fake()->createWithContent('bad.php', '<?php echo 1;')])->assertUnprocessable();
        $this->assertDatabaseHas('reqDokumen', ['id' => $id, 'status' => 'pending']);
    }

    public function test_pages_render_and_request_create_does_not_redirect(): void
    {
        $this->actingAs($this->department)->get(route('jurusan.dokumen.create'))->assertOk()->assertSee('Daftar Nilai')->assertSee('Kehadiran');
        $this->get(route('jurusan.dokumen.index'))->assertOk()->assertSee('Upload Dokumen Akademik');
        $this->actingAs($this->student)->get(route('mahasiswa.request.create'))->assertOk()->assertSee('Document Request Form');
        $this->get(route('mahasiswa.request.index'))->assertOk();
        $this->get(route('mahasiswa.dokumen.index'))->assertOk();
        $id = $this->newRequest();
        $this->actingAs($this->kln)->get(route('kln.dokumen.page'))->assertOk()->assertSee('Keperluan administrasi kampus.');
        $this->deleteJson(route('kln.dokumen.destroy', $id))->assertOk();
        $this->assertDatabaseMissing('reqDokumen', ['id' => $id]);
    }
    public function test_failed_notification_rolls_back_document_and_removes_new_file(): void
    {
        $this->app->instance(\App\Services\DocumentNotification::class, new class extends \App\Services\DocumentNotification {
            public function student(int $studentId, string $subject, string $message): void
            {
                throw new \RuntimeException('Simulated write failure');
            }
        });
        $this->actingAs($this->department)->post(route('jurusan.dokumen.store'), $this->academic())->assertStatus(500);
        $this->assertDatabaseCount('dokumen', 0);
        $this->assertDatabaseCount('fileDetail', 0);
        $this->assertDatabaseCount('historyDokumen', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_csrf_is_enforced_outside_testing_mode(): void
    {
        $this->app->instance('env', 'production');
        $this->actingAs($this->student)->post(route('mahasiswa.request.store'), ['tipeDkmn' => 'KRS', 'message' => 'Test'])->assertStatus(419);
        $this->actingAs($this->department)->post(route('jurusan.dokumen.store'), $this->academic())->assertStatus(419);
        $this->actingAs($this->kln)->postJson(route('kln.dokumen.reject', 1), ['keterangan' => 'Test'])->assertStatus(419);
        $this->assertDatabaseCount('reqDokumen', 0);
        $this->assertDatabaseCount('dokumen', 0);
    }

}
