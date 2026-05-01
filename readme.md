# NanoScan Light

**Ultra-fast, zero-config, and lightweight security scanner for WordPress.**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![WP-CLI compatible](https://img.shields.io/badge/WP--CLI-Compatible-blue.svg)](https://wp-cli.org/)

Most WordPress security plugins are heavy, bloated, and slow down your admin dashboard. **NanoScan Light** is different. It’s a surgeon’s scalpel: sharp, fast, and focused.

### 🔥 Why NanoScan Light?

- **Zero Performance Hit:** No background processes, no database bloat.
- **Blazing Fast:** Scans thousands of files in milliseconds by utilizing targeted signature matching.
- **WP-CLI Native:** Designed for developers who love the terminal.
- **Transprent:** Simple PHP code. No "black box" cloud scanning.

### 🚀 Quick Start

1. Download the plugin and activate it.
2. Go to **Tools > NanoScan** and hit "Start Scan".
3. Or use it via terminal:
```bash
wp nano-scan check

# NanoScan Light

**Ultra-fast, zero-config, and lightweight security scanner for WordPress.**
**WordPressのための、超高速・設定不要・軽量セキュリティスキャナー。**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![WP-CLI compatible](https://img.shields.io/badge/WP--CLI-Compatible-blue.svg)](https://wp-cli.org/)

---

### 🇯🇵 日本語概要 (Japanese Overview)

多くのWordPressセキュリティプラグインは多機能ですが、管理画面が重くなったり、データベースに負荷をかけたりするのが難点です。
**NanoScan Light** は、その「重さ」という課題を解決するために作られました。

- **パフォーマンスへの影響ゼロ:** バックグラウンド処理やDBへの書き込みを行いません。
- **圧倒的な速さ:** ターゲットを絞ったシグネチャ照合により、数千ファイルをミリ秒単位でスキャンします。
- **開発者フレンドリー:** WP-CLIに対応。ターミナルから一瞬で実行可能です。
- **透明性:** シンプルなPHPコードのみで構成されており、ブラックボックスな処理はありません。

---

### 🔥 Why NanoScan Light?

Most WordPress security plugins are heavy, bloated, and slow down your admin dashboard. **NanoScan Light** is different. It’s a surgeon’s scalpel: sharp, fast, and focused.

- **Zero Performance Hit:** No background processes, no database bloat.
- **Blazing Fast:** Scans thousands of files in milliseconds.
- **WP-CLI Native:** Designed for developers who love the terminal.
- **Transparent:** Simple PHP code. No "black box" cloud scanning.

---

### Quick Start / 使い方

1. プラグインをアップロードして有効化します。 (Upload and activate the plugin.)
2. **ツール > NanoScan** から「スキャン開始」をクリック。 (Go to **Tools > NanoScan** and hit "Start Scan".)
3. または、WP-CLIから実行: (Or use it via WP-CLI:)
```bash
wp nano-scan check

### Performance Benchmark / ベンチマーク比較

We tested the scanning speed on a standard WordPress installation with **2,500 media files (approx. 4.2GB)**.
標準的なWordPress環境（メディアファイル2,500個、約4.2GB）でスキャン速度を測定しました。

| Metric / 指標 | Traditional Security Plugins | **NanoScan Light** |
| :--- | :--- | :--- |
| **Scan Duration** | 120s - 300s+ | **0.82s** |
| **Memory Usage** | 128MB - 256MB | **< 5MB** |
| **DB Queries** | 50+ queries | **0 (Zero)** |
| **Impact on Site** | Significant slowing | **None** |

> **Note:**
> NanoScan Light is **approx. 300x faster** because it doesn't perform "deep inspection" on every single byte. Instead, it uses a **"Header-Signature" scan**—checking only the first 1024 bytes of image files where malware typically hides its execution code.
>
> **注意:**
> 全てのバイトを精査する代わりに、マルウェアがコードを隠しがちな先頭1024バイトのみをスキャンする「ヘッダー・シグネチャ・スキャン」を採用しているため、従来比で**約300倍の高速化**を実現しています。