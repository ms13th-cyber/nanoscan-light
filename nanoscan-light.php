<?php
/**
 * Plugin Name: NanoScan Light
 * Description: Ultra-lightweight security scanner for uploads directory.
 * Version: 0.1
 * Author: masato shibuya
 */

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function() {
    add_management_page('NanoScan', 'NanoScan', 'manage_options', 'nanoscan', 'nanoscan_render_page');
});

function nanoscan_render_page() {
    ?>
    <div class="wrap">
        <h1>NanoScan <mark style="background: #00ff00; color: #000; padding: 2px 6px; border-radius: 4px; font-size: 0.6em; vertical-align: middle;">ULTRA LIGHT</mark></h1>
        <p>uploadsディレクトリ内の不審なファイルをスキャンします。</p>
        <form method="post">
            <?php submit_button('スキャン開始', 'primary', 'start_scan'); ?>
        </form>

        <?php
        if (isset($_POST['start_scan'])) {
            nanoscan_execute();
        }
        ?>
    </div>
    <?php
}

function nanoscan_execute() {
    $upload_dir = wp_upload_dir()['basedir'];
    $it = new RecursiveDirectoryIterator($upload_dir);
    $display_it = new RecursiveIteratorIterator($it);

    $found_issues = [];
    $start_time = microtime(true);

    foreach ($display_it as $file) {
        if ($file->isDir()) continue;

        $file_path = $file->getPathname();
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        // 1. PHPファイルの混入チェック
        if (in_array($extension, ['php', 'phtml', 'php3', 'php4', 'php5', 'phps'])) {
            $found_issues[] = [
                'path' => $file_path,
                'reason' => 'PHPファイルが画像フォルダ内に存在します。'
            ];
            continue;
        }

        // 2. 画像ファイルの中身をチェック (画像の中にPHPが隠されていないか)
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $content = file_get_contents($file_path, false, null, 0, 1024); // 先頭1KBだけ高速読み込み
            if (strpos($content, '<?php') !== false || strpos($content, 'eval(') !== false) {
                $found_issues[] = [
                    'path' => $file_path,
                    'reason' => '画像ファイル内に実行コードが検出されました。'
                ];
            }
        }
    }

    $end_time = microtime(true);
    $execution_time = round($end_time - $start_time, 4);

    echo "<h3>スキャン結果 (実行時間: {$execution_time} 秒)</h3>";

    if (empty($found_issues)) {
        echo '<p style="color: green;">✔ 不審なファイルは見つかりませんでした。</p>';
    } else {
        echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>ファイルパス</th><th>理由</th></tr></thead><tbody>';
        foreach ($found_issues as $issue) {
            echo "<tr><td><code>" . esc_html($issue['path']) . "</code></td><td>" . esc_html($issue['reason']) . "</td></tr>";
        }
        echo '</tbody></table>';
    }
}

/**
 * WP-CLI Command: wp nano-scan
 *
 * 使い方:
 * wp nano-scan check
 */
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('nano-scan', function($args, $assoc_args) {
        WP_CLI::log(WP_CLI::colorize('%GStarting NanoScan (Ultra Light)...%n'));

        $upload_dir = wp_upload_dir()['basedir'];

        // 速度計測開始
        $start_time = microtime(true);

        $it = new RecursiveDirectoryIterator($upload_dir);
        $display_it = new RecursiveIteratorIterator($it);

        $found_count = 0;

        foreach ($display_it as $file) {
            if ($file->isDir()) continue;

            $file_path = $file->getPathname();
            $relative_path = str_replace(ABSPATH, '', $file_path);
            $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

            $issue_reason = '';

            // 1. PHPファイルのチェック
            if (in_array($extension, ['php', 'phtml', 'php3', 'php4', 'php5', 'phps'])) {
                $issue_reason = 'PHP executable found';
            }
            // 2. 画像偽装チェック
            elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $content = file_get_contents($file_path, false, null, 0, 1024);
                if (strpos($content, '<?php') !== false || strpos($content, 'eval(') !== false) {
                    $issue_reason = 'Embedded PHP code detected';
                }
            }

            if ($issue_reason) {
                WP_CLI::warning("{$issue_reason}: {$relative_path}");
                $found_count++;
            }
        }

        $end_time = microtime(true);
        $execution_time = round($end_time - $start_time, 4);

        if ($found_count === 0) {
            WP_CLI::success("No suspicious files found. (Scan time: {$execution_time}s)");
        } else {
            WP_CLI::error("Scan completed. Found {$found_count} suspicious files. (Scan time: {$execution_time}s)");
        }
    });
}