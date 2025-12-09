<?php
// /opt/emby_signup/media_manager.php
session_start();

// 检查管理员登录
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php?admin=1');
    exit;
}

require_once 'config.php';
require_once 'emby_functions.php';

$config = include 'config.php';

// 处理表单提交
$message = '';
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operation = $_POST['operation'] ?? '';
    $selected_libraries = $_POST['selected_libraries'] ?? [];
    $test_users_input = trim($_POST['test_users'] ?? '');
    
    // 获取测试用户
    $test_users = [];
    if (!empty($test_users_input)) {
        $test_users = array_filter(array_map('trim', explode(',', $test_users_input)));
    }
    
    if (empty($operation)) {
        $message = "请选择操作类型（显示或隐藏）";
    } elseif (empty($selected_libraries)) {
        $message = "请至少选择一个媒体库";
    } else {
        // 执行操作
        if ($operation === 'show') {
            $result = show_libraries_for_users($selected_libraries, $test_users);
        } elseif ($operation === 'hide') {
            $result = hide_libraries_for_users($selected_libraries, $test_users);
        }
        
        if (isset($result)) {
            $message = $result['message'];
            $results = $result['results'] ?? [];
        }
    }
}

// 获取所有媒体库列表
list($library_map) = get_all_libraries();
if (empty($library_map)) {
    $library_map = [];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>媒体库权限管理</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), 
            url('<?php echo $config['site']['custom_image']; ?>') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.8;
        }

        .admin-panel {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 16px;
        }

        .form-group .sub-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: normal;
            margin-top: 4px;
        }

        .operation-select {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .operation-option {
            flex: 1;
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .operation-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .operation-option.active {
            border-color: #667eea;
            background: #eff6ff;
        }

        .operation-option.show.active {
            border-color: #10b981;
            background: #ecfdf5;
        }

        .operation-option.hide.active {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .operation-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .operation-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .operation-desc {
            font-size: 12px;
            color: #6b7280;
        }

        .library-checkboxes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 10px;
        }

        .library-checkbox {
            display: flex;
            align-items: center;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .library-checkbox:hover {
            border-color: #9ca3af;
            transform: translateY(-2px);
        }

        .library-checkbox.selected {
            border-color: #667eea;
            background: #eff6ff;
        }

        .library-checkbox input {
            margin-right: 12px;
            width: 18px;
            height: 18px;
        }

        .library-name {
            font-weight: 500;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            background: <?php echo $config['site']['theme']['primary_gradient']; ?>;
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .message {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: <?php echo $config['site']['theme']['success_color']; ?>;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 15px;
        }

        .message.error {
            background: #fee2e2;
            border-color: #fecaca;
            color: <?php echo $config['site']['theme']['error_color']; ?>;
        }

        .message.warning {
            background: #fef3c7;
            border-color: #fde68a;
            color: <?php echo $config['site']['theme']['warning_color']; ?>;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        .results-table th, .results-table td {
            padding: 12px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .results-table th {
            background: #f3f4f6;
            font-weight: 600;
        }

        .status-success {
            color: #065f46;
            background: #d1fae5;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status-failed {
            color: #dc2626;
            background: #fee2e2;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .nav-btn {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .nav-btn.primary {
            background: <?php echo $config['site']['theme']['primary_gradient']; ?>;
            color: white;
            border: none;
        }

        .nav-btn.primary:hover {
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .library-id {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
            font-family: monospace;
        }

        .select-all {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            background: #f3f4f6;
            width: fit-content;
        }

        .select-all:hover {
            background: #e5e7eb;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .admin-panel {
                padding: 25px;
            }
            
            .operation-select {
                flex-direction: column;
            }
            
            .library-checkboxes {
                grid-template-columns: 1fr;
            }
            
            .nav-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>媒体库权限管理</h1>
            <p>统一管理用户对媒体库的访问权限</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php 
                echo strpos($message, '失败') !== false ? 'error' : 
                (strpos($message, '警告') !== false ? 'warning' : '');
            ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="admin-panel">
            <form method="post" id="media-form">
                <!-- 第一步：选择操作类型 -->
                <div class="form-group">
                    <label>选择操作类型</label>
                    <div class="operation-select">
                        <div class="operation-option show" data-operation="show">
                            <div class="operation-icon">👁️</div>
                            <div class="operation-title">显示模式</div>
                            <div class="operation-desc">只显示选中的媒体库</div>
                        </div>
                        <div class="operation-option hide" data-operation="hide">
                            <div class="operation-icon">🔒</div>
                            <div class="operation-title">隐藏模式</div>
                            <div class="operation-desc">隐藏选中的媒体库</div>
                        </div>
                    </div>
                    <input type="hidden" name="operation" id="operation-input" value="">
                </div>

                <!-- 第二步：选择媒体库 -->
                <div class="form-group">
                    <label>选择媒体库 <span class="sub-label">（可多选）</span></label>
                    
                    <?php if (!empty($library_map)): ?>
                        <div class="select-all" id="select-all">
                            <input type="checkbox" id="select-all-checkbox">
                            <span>全选/取消全选</span>
                        </div>
                        
                        <div class="library-checkboxes">
                            <?php foreach ($library_map as $name => $id): ?>
                                <label class="library-checkbox">
                                    <input type="checkbox" name="selected_libraries[]" value="<?php echo htmlspecialchars($name); ?>">
                                    <div>
                                        <div class="library-name"><?php echo htmlspecialchars($name); ?></div>
                                        <div class="library-id">ID: <?php echo htmlspecialchars(substr($id, 0, 8)); ?>...</div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="padding: 20px; text-align: center; background: #f3f4f6; border-radius: 10px; color: #6b7280;">
                            无法获取媒体库列表，请检查 Emby 服务器连接
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 第三步：输入测试用户 -->
                <div class="form-group">
                    <label>指定用户 <span class="sub-label">（可选，用逗号分隔用户名，留空则影响所有用户）</span></label>
                    <input type="text" name="test_users" 
                           placeholder="例如: user1, user2, user3">
                </div>

                <!-- 提交按钮 -->
                <button type="submit" class="btn" id="submit-btn" disabled>
                    执行操作
                </button>
            </form>

            <!-- 操作结果 -->
            <?php if (!empty($results)): ?>
            <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #e5e7eb;">
                <h3 style="margin-bottom: 20px; color: #374151;">操作结果</h3>
                
                <div style="margin-bottom: 15px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                    <strong>统计：</strong>
                    <?php 
                    $success_count = 0;
                    $failed_count = 0;
                    
                    foreach ($results as $result) {
                        if ($result['status'] === 'success') $success_count++;
                        elseif ($result['status'] === 'failed') $failed_count++;
                    }
                    ?>
                    <span style="color: #10b981;">成功: <?php echo $success_count; ?></span> | 
                    <span style="color: #ef4444;">失败: <?php echo $failed_count; ?></span>
                </div>
                
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>用户名</th>
                                <th>状态</th>
                                <th>原因</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $count = 0;
                            foreach ($results as $username => $result):
                                if ($count >= 50) break;
                                $count++;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($username); ?></td>
                                <td>
                                    <span class="status-<?php echo $result['status']; ?>">
                                        <?php echo $result['status'] === 'success' ? '成功' : '失败'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($result['reason'] ?? '-'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- 导航按钮 -->
            <div class="nav-buttons">
                <a href="index.php?admin=1" class="nav-btn">返回邀请码管理</a>
                <a href="index.php?admin=1&page=dashboard" class="nav-btn primary">返回管理面板</a>
                <a href="index.php" class="nav-btn">返回注册页面</a>
                <a href="index.php?action=logout" class="nav-btn">退出管理</a>
            </div>
        </div>
    </div>

    <script>
        // 选择操作类型
        document.querySelectorAll('.operation-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.operation-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                
                this.classList.add('active');
                document.getElementById('operation-input').value = this.dataset.operation;
                updateSubmitButton();
            });
        });

        // 全选功能
        document.getElementById('select-all-checkbox').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_libraries[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                updateCheckboxUI(checkbox);
            });
            updateSubmitButton();
        });

        // 单个复选框点击事件
        document.querySelectorAll('input[name="selected_libraries[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateCheckboxUI(this);
                updateSelectAllCheckbox();
                updateSubmitButton();
            });
            updateCheckboxUI(checkbox);
        });

        // 全选框点击事件
        document.getElementById('select-all').addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = document.getElementById('select-all-checkbox');
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });

        // 更新复选框UI
        function updateCheckboxUI(checkbox) {
            const label = checkbox.closest('.library-checkbox');
            if (checkbox.checked) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
        }

        // 更新全选框状态
        function updateSelectAllCheckbox() {
            const checkboxes = document.querySelectorAll('input[name="selected_libraries[]"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const someChecked = Array.from(checkboxes).some(cb => cb.checked);
            
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }

        // 更新提交按钮状态
        function updateSubmitButton() {
            const operation = document.getElementById('operation-input').value;
            const selectedLibraries = document.querySelectorAll('input[name="selected_libraries[]"]:checked');
            const submitBtn = document.getElementById('submit-btn');
            
            if (operation && selectedLibraries.length > 0) {
                submitBtn.disabled = false;
                submitBtn.textContent = operation === 'show' ? 
                    '👁️ 执行显示操作' : '🔒 执行隐藏操作';
            } else {
                submitBtn.disabled = true;
                submitBtn.textContent = '执行操作';
            }
        }

        // 表单提交确认
        document.getElementById('media-form').addEventListener('submit', function(e) {
            const operation = document.getElementById('operation-input').value;
            const selectedLibraries = document.querySelectorAll('input[name="selected_libraries[]"]:checked');
            const libraryNames = Array.from(selectedLibraries).map(cb => cb.value);
            
            let message = '';
            if (operation === 'show') {
                message = `确定要设置为只显示以下媒体库吗？\n\n${libraryNames.join(', ')}`;
            } else if (operation === 'hide') {
                message = `确定要隐藏以下媒体库吗？\n\n${libraryNames.join(', ')}`;
            }
            
            if (!confirm(message)) {
                e.preventDefault();
            }
        });

        // 初始化
        updateSubmitButton();
        updateSelectAllCheckbox();
    </script>
</body>
</html>
