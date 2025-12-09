<?php
// /opt/emby_signup/config.php
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
        'preset_userid' => 'user_id',  // 预设用户ID（模板用户）
        // 网页登录Emby，点进用户界面，浏览器输入框usre_id=后面的字符串
        'admin_password' => 'admin',                       // 管理员密码
        'invite_file' => 'invite_codes.json',                   // 邀请码存储文件
        'default_password_min_length' => 4,                     // 默认密码最小长度
        'default_username_pattern' => '/^[a-zA-Z0-9]{4,}$/',    // 用户名正则规则
    ],
    
    // ========== 网站前端配置 ==========
    'site' => [
        'name' => 'EmbyPanel',                                   // 网站名称
        'title' => 'Emby Panel',                           // 页面标题
        'emby_login_url' => 'https://emby.com',                // Emby 登录地址
        'custom_image' => 'https://www.loliapi.com/acg/pe/',    // 背景图片API
        'favicon' => 'https://emby.media/favicon-96x96.png',    // 网站图标
        'theme' => [                                            // 主题颜色
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
        'code_length' => 8,                                     // 邀请码长度
        'allowed_chars' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',  // 允许的字符集
        'auto_generate_link' => true,                           // 是否自动生成邀请链接
    ],
    
    // ========== 系统设置 ==========
    'system' => [
        'debug_mode' => false,                                  // 调试模式
        'session_timeout' => 3600,                              // 会话超时时间（秒）
        'enable_error_logging' => true,                         // 是否启用错误日志
        'timezone' => 'Asia/Shanghai',                          // 时区设置
    ],
    
    // ========== 安全配置 ==========
    'security' => [
        'rate_limit' => [                                       // 频率限制
            'register' => 5,      // 每分钟最多注册次数
            'admin_login' => 3,   // 每分钟最多管理员登录次数
        ],
        'password_requirements' => [                            // 密码要求
            'min_length' => 4,
            'require_numbers' => false,
            'require_special_chars' => false,
        ],
        'csrf_protection' => true,                             // CSRF保护
    ],
];
?>
