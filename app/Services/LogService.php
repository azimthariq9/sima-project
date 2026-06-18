<?php
//app/Services/LogService.php
namespace App\Services;

use App\Models\Log;




class LogService extends BaseService
{
    
    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10): array
    {
        return Log::with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($log) {
                return (object)[
                    'action' => $log->description,
                    'actor' => $log->user->name ?? 'System',
                    'time' => $log->created_at->diffForHumans(),
                    'icon' => $this->getIconForAction($log->action),
                    'bg' => $this->getBgColorForAction($log->action),
                    'color' => $this->getTextColorForAction($log->action),
                ];
            })->toArray();
    }

    private function getIconForAction($action): string
    {
        $icons = [
            'CREATE' => 'fa-plus-circle',
            'UPDATE' => 'fa-edit',
            'DELETE' => 'fa-trash',
            'UPLOAD' => 'fa-upload',
            'DOWNLOAD' => 'fa-download',
            'VERIFY' => 'fa-check-circle',
            'REJECT' => 'fa-times-circle',
            'LOGIN' => 'fa-sign-in-alt',
            'LOGOUT' => 'fa-sign-out-alt',
        ];
        
        return $icons[$action] ?? 'fa-circle-info';
    }

    private function getBgColorForAction($action): string
    {
        $colors = [
            'CREATE' => '#E6F7E6',
            'UPDATE' => '#FFF3E0',
            'DELETE' => '#FFEBEE',
            'VERIFY' => '#E3F2FD',
            'REJECT' => '#FFEBEE',
            'LOGIN' => '#E8F5E8',
        ];
        
        return $colors[$action] ?? '#F5F5F5';
    }

    private function getTextColorForAction($action): string
    {
        $colors = [
            'CREATE' => '#2E7D32',
            'UPDATE' => '#F57C00',
            'DELETE' => '#C62828',
            'VERIFY' => '#1565C0',
            'REJECT' => '#C62828',
            'LOGIN' => '#1B5E20',
        ];
        
        return $colors[$action] ?? '#424242';
    }
}