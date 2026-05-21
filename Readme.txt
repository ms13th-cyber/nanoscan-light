=== NanoScan Light ===
Contributors: masato shibuya(Image-box Co., Ltd.)
Tags: security, scanner, performance, malware, light, forensic
Requires at least: 5.0
Tested up to: 7.0.0
Requires PHP: 8.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

超軽量・爆速。uploadsディレクトリに特化した、サイトを重くしない実戦型セキュリティスキャナー。

== Description ==

**NanoScan Light** は、肥大化したセキュリティプラグインに疑問を持つ開発者のために設計された、パフォーマンス特化型のフォレンジックツールです。

「スキャンのためにサイトを重くする」という本末転倒な状況を打破するため、最も攻撃を受けやすい `uploads` ディレクトリに照準を絞り、圧倒的なレスポンスで不審なファイルを特定します。

v1.0.0 では、軽量性を維持したまま AJAX インターフェースを導入。数万件のファイルが存在する大規模サイトでも、ブラウザのタイムアウトを気にせずスキャンが可能になりました。

主な特徴：

*   **ヘッダー・シグネチャ・スキャン**: ファイルの先頭ブロックのみをピンポイントで解析する独自ロジック。
*   **AJAX プログレッシブ・インターフェース**: スキャン進捗をリアルタイムで表示。サーバー負荷を分散しつつ、確実な実行を保証。
*   **多層的な検知ロジック**: PHP混入、画像偽装、実行権限を書き換える `.htaccess`、さらに SVG 内に隠されたスクリプトまで検知。
*   **WP-CLI 準拠**: プロフェッショナルのための CLI コマンドを完備。自動化スクリプトへの組み込みも容易。
*   **Zero-Resource Policy**: スキャン実行時以外はメモリもDBも一切消費しない「完全な静止状態」を維持。

== Benchmark (Performance) ==

標準的なWordPress環境（2,500ファイル / 約4.2GB）での比較：

*   **一般的なプラグイン**: スキャン時間 120s〜300s / サイト動作への影響 有
*   **NanoScan Light**: スキャン時間 **0.8s - 1.2s** / サイト動作への影響 **皆無**

== Installation ==

1. プラグインフォルダを `wp-content/plugins/nanoscan-light/` に配置します。
2. WordPress管理画面の「プラグイン」から有効化します。
3. 「ツール > NanoScan」からスキャンを実行してください。

== Usage ==

=== 管理画面から使用する場合 ===
「ツール > NanoScan」を開き、「スキャン開始」をクリックします。非同期通信（AJAX）により、ページをリロードすることなく結果が表示されます。

=== WP-CLIから使用する場合 ===
ターミナルで以下のコマンドを実行してください：
`wp nano-scan`

== Notes ==

*   **スコープの限定**: 本プラグインは `uploads` ディレクトリおよびバイナリデータの先頭解析に最適化されています。フルスキャンではなく、日常的な異常検知のファーストラインとしてご利用ください。
*   **安全設計**: 自動削除機能は非搭載です。検知されたファイルは管理者が手動で検証することを前提としています。

== Changelog ==

= 1.0.1 =
* Wordpress7.0.0での動作確認。

= 1.0.0 =
*   **Major Update**: AJAXベースのスキャンエンジンへの刷新。
*   **SVG Support**: XMLベースの画像ファイル（SVG）内へのコード埋め込み検知に対応。
*   **Config Protection**: uploads 内の不正な `.htaccess` 設置の検知を追加。
*   **Advanced Patterns**: `base64_decode`, `gzinflate`, `str_rot13` 等の難読化パターン検知を強化。
*   スキャン進捗を示すプログレスバーの実装。
*   セキュリティ向上のための Nonce チェック導入。
*   JavaScript による多重実行防止機能の追加。
*   UI のブラッシュアップ。

= 0.1.0 =
*   初版リリース。
*   超高速シグネチャスキャンエンジンの実装。
*   WP-CLIコマンドの実装。

== License ==

This plugin is licensed under the GPLv2 or later.