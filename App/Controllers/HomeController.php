<?php
namespace App\Controllers;

use System\Core\Render;

class HomeController extends Render
{
    public function index()
    {
        $data = [
            'title' => 'Welcome to Coolscript MVC Framework',
            'version' => '0.01',
            'features' => [
                [
                    'icon' => '🚀',
                    'title' => 'Fast & Lightweight',
                    'description' => 'Built for performance with minimal overhead and quick response times.'
                ],
                [
                    'icon' => '🔒',
                    'title' => 'Secure by Default',
                    'description' => 'CSRF protection, HTTPS enforcement, secure headers, and CSP out of the box.'
                ],
                [
                    'icon' => '🎯',
                    'title' => 'Smart Routing',
                    'description' => 'Manual route registration with support for parameters and custom patterns.'
                ],
                [
                    'icon' => '🛠️',
                    'title' => 'Developer Friendly',
                    'description' => 'PSR-4 autoloading, helper classes, and extensive documentation.'
                ],
                [
                    'icon' => '💾',
                    'title' => 'Flexible Sessions',
                    'description' => 'Choose between cookie-based or database-backed session storage.'
                ],
                [
                    'icon' => '🎨',
                    'title' => 'Bootstrap 5 Ready',
                    'description' => 'Pre-integrated with Bootstrap 5 for rapid UI development.'
                ]
            ],
            'quickstart' => [
                'Create a controller in <code>App/Controllers/</code>',
                'Add your view in <code>App/Views/</code>',
                'Configure your app in <code>App/Config/settings.php</code>',
                'Define your routes in <code>App/Config/routes.php</code> for each endpoint.'
            ]
        ];
        
        return $this->render('home', $data);
    }
}
