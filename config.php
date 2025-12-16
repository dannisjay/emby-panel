<?php
// /opt/emby-panel/config.php
// 统一配置文件 - 所有配置信息集中管理

return [
    // ========== Emby 服务器配置 ==========
    'emby' => [
        'host' => 'http://ip:8096',           // Emby 服务器地址
        'api_key' => 'xxxxxxxxxxxxxxxxxxxx',  // Emby API Key
        'server_name' => 'Emby',                      // 服务器名称
    ],
    
    // ========== 用户系统配置 ==========
    'user' => [
        'preset_userid' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',  // 预设用户ID（模板用户）
        'admin_username' => 'admin',                            // 管理员账户
        'admin_password' => 'admin123',                         // 管理员密码
        'invite_file' => 'invite_codes.json',                   // 邀请码存储文件
        'default_password_min_length' => 4,                     
        'default_username_pattern' => '/^[a-zA-Z0-9]{4,}$/',   
    ],

    // ========== 账号清理配置 ==========
    'cleanup' => [
        'inactive_days' => 60,               // 多少天没有登录自动删除
        'enable_inactive_cleanup' => true,   // 是否启用未登录账号清理
        'skip_never_logged_in' => false,     // 是否跳过从未登录的账号
        'skip_admins' => true,               // 是否跳过管理员账号
        'log_file' => 'logs/inactive_cleanup_log.txt', // 清理日志文件
    ],

    // ========== 网站前端配置 ==========
    'site' => [
        'name' => 'Emby',                                   // 网站名称
        'title' => 'Emby Signup',                           // 页面标题
        'emby_login_url' => 'https://emby.com',             // Emby 登录地址
        'custom_image' => 'https://www.loliapi.com/acg/pe/',    // 背景API
        'favicon' => 'https://emby.media/favicon-96x96.png',    // 网站图标
        'theme' => [                                            
            'primary_gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'success_color' => '#10b981',
            'error_color' => '#ef4444',
            'warning_color' => '#f59e0b',
        ],
    ],
    
    // ========== 媒体库管理配置 ==========
    'media' => [
        'default_mode' => 'HIDE',                               // 默认操作模式
        'skip_admin' => true,                                   // 是否跳过管理员用户
        'api_timeout' => 10,                                    // API超时时间（秒）
        'max_retries' => 2,                                     // API最大重试次数
    ],
    
    // ========== 邀请码系统配置 ==========
    'invite' => [
        'code_length' => 8,                                     
        'allowed_chars' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789', 
        'auto_generate_link' => true,                         
        'default_valid_days' => 7,                              // 默认有效期天数（新增）
        'max_valid_days' => 360,                                // 最大有效期天数（新增）
    ],
    
    // ========== 系统设置 ==========
    'system' => [
        'debug_mode' => false,                             
        'session_timeout' => 3600,                           
        'enable_error_logging' => true,                     
        'timezone' => 'Asia/Shanghai',                      
    ],
    
    // ========== 安全配置 ==========
    'security' => [
        'rate_limit' => [                                     
            'register' => 5,      
            'admin_login' => 3, 
        ],
        'password_requirements' => [                          
            'min_length' => 4,
            'require_numbers' => false,
            'require_special_chars' => false,
        ],
        'csrf_protection' => true,                             // CSRF保护
    ],
];
?>
