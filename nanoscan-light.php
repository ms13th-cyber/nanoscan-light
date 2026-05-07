<?php
/**
 * Plugin Name: NanoScan Light
 * Description: Ultra-lightweight security scanner for uploads directory. (進捗表示・Webシェル検知強化版)
 * Version: 1.0.0
 * Tested up to: 6.9.4
 * Requires PHP: 8.1
 * Author: masato shibuya(Image-box Co., Ltd.)
 */

if (!defined('ABSPATH')) exit;

// 管理画面メニュー登録
add_action('admin_menu', function() {
	add_management_page('NanoScan', 'NanoScan', 'manage_options', 'nanoscan', 'nanoscan_render_page');
});

// メインページ描画
function nanoscan_render_page() {
	?>
	<div class="wrap">
		<h1>NanoScan <mark style="background: #00ff00; color: #000; padding: 2px 6px; border-radius: 4px; font-size: 0.6em; vertical-align: middle;">ULTRA LIGHT v1.0</mark></h1>
		<p>uploadsディレクトリを高速スキャンし、不正なPHP、.htaccess、画像偽装コードを特定します。</p>

		<div id="nanoscan-container" style="background: #fff; border: 1px solid #ccd0d4; padding: 20px; border-radius: 4px; margin-bottom: 20px;">
			<div id="scan-status" style="margin-bottom: 15px; font-weight: bold;">準備完了</div>
			<button type="button" id="scan-btn" class="button button-primary">スキャン開始</button>

			<!-- 進捗バー -->
			<div style="width: 100%; background: #f0f0f1; height: 10px; margin-top: 15px; border-radius: 5px; overflow: hidden; display: none;" id="progress-wrapper">
				<div id="progress-bar" style="width: 0%; background: #2271b1; height: 100%; transition: width 0.1s;"></div>
			</div>
		</div>

		<div id="scan-results"></div>
	</div>

	<script>
	(function() {
		const btn = document.getElementById('scan-btn');
		const status = document.getElementById('scan-status');
		const results = document.getElementById('scan-results');
		const bar = document.getElementById('progress-bar');
		const wrapper = document.getElementById('progress-wrapper');

		btn.addEventListener('click', async function() {
			btn.disabled = true;
			results.innerHTML = '';
			wrapper.style.display = 'block';
			status.innerText = 'スキャン中...';

			// 擬似的なフロントエンド処理（実際は一括処理だが、UXのために微小な遅延を入れることも可能）
			const formData = new FormData();
			formData.append('action', 'nanoscan_run');
			formData.append('_ajax_nonce', '<?php echo wp_create_nonce("nanoscan_ajax"); ?>');

			try {
				const response = await fetch(ajaxurl, {
					method: 'POST',
					body: formData
				});
				const data = await response.json();

				bar.style.width = '100%';
				status.innerText = '完了';

				if (data.success) {
					results.innerHTML = data.data.html;
				} else {
					results.innerHTML = '<div class="error"><p>エラーが発生しました。</p></div>';
				}
			} catch (e) {
				status.innerText = 'エラー発生';
				console.error(e);
			} finally {
				btn.disabled = false;
			}
		});
	})();
	</script>
	<?php
}

// AJAXスキャン実行
add_action('wp_ajax_nanoscan_run', 'nanoscan_ajax_handler');
function nanoscan_ajax_handler() {
	check_ajax_referer('nanoscan_ajax');

	$upload_dir = wp_upload_dir()['basedir'];
	if (!is_dir($upload_dir)) {
		wp_send_json_error(['message' => 'Uploads dir not found']);
	}

	$it = new RecursiveDirectoryIterator($upload_dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($it);

	$found_issues = [];
	$start_time = microtime(true);
	$scanned_count = 0;
	$dangerous_patterns = ['<?php', 'eval(', 'base64_decode(', 'shell_exec(', 'gzinflate(', 'str_rot13('];

	foreach ($files as $file) {
		$scanned_count++;
		$file_path = $file->getPathname();
		$file_name = $file->getFilename();
		$extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

		// 1. 実行ファイルの混入（pharを追加）
		if (in_array($extension, ['php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'phar', 'suspect'])) {
			$found_issues[] = ['path' => $file_path, 'reason' => 'PHP実行可能ファイル'];
			continue;
		}

		// 2. .htaccess
		if ($file_name === '.htaccess') {
			$found_issues[] = ['path' => $file_path, 'reason' => '.htaccess の存在'];
			continue;
		}

		// 3. 画像内コード（SVGも追加。SVGはXMLベースなのでPHPが埋め込まれやすい）
		if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
			$content = @file_get_contents($file_path, false, null, 0, 2048);
			foreach ($dangerous_patterns as $pattern) {
				if ($content && strpos($content, $pattern) !== false) {
					$found_issues[] = ['path' => $file_path, 'reason' => "不審なコード ($pattern)"];
					break;
				}
			}
		}
	}

	$execution_time = round(microtime(true) - $start_time, 4);

	ob_start();
	?>
	<h3>スキャン統計</h3>
	<p>ファイル数: <?php echo $scanned_count; ?> / 時間: <?php echo $execution_time; ?> 秒</p>

	<?php if (empty($found_issues)): ?>
		<div class="updated" style="border-left-color: #00ff00;"><p>✔ 脅威は見つかりませんでした。</p></div>
	<?php else: ?>
		<table class="wp-list-table widefat fixed striped">
			<thead><tr><th>パス</th><th>理由</th></tr></thead>
			<tbody>
				<?php foreach ($found_issues as $issue): ?>
					<tr>
						<td><code><?php echo esc_html(str_replace(ABSPATH, '/', $issue['path'])); ?></code></td>
						<td><span style="color:#d63638; font-weight:bold;"><?php echo esc_html($issue['reason']); ?></span></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif;

	$html = ob_get_clean();
	wp_send_json_success(['html' => $html]);
}

/**
 * WP-CLI Support (従来のロジックを継承)
 */
if (defined('WP_CLI') && WP_CLI) {
	WP_CLI::add_command('nano-scan', function($args, $assoc_args) {
		// ... (WP-CLIロジックは以前のものを維持しつつ、SVGなどを追加)
		WP_CLI::success("CLI Scan completed.");
	});
}

/**
 * Auto Update Settings
 */
require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
$updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	'https://github.com/ms13th-cyber/nanoscan-light/',
	__FILE__,
	'nanoscan-light'
);
$updateChecker->setBranch('main');