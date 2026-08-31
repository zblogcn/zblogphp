# Security Policy / 安全政策

## Supported Versions / 支持版本

Only the latest master branch or the latest release tag is supported.

仅限最新的 master 分支或最新的 Release 标签。

## 项目维护状态

Z-BlogPHP is currently in **Limited Maintenance Mode**. We prioritize critical security vulnerabilities but **cannot guarantee response times or fix timelines**. Low-risk problems may not be addressed promptly. Thank you for your understanding.

本项目目前处于**有限维护状态**。我们会优先关注高危安全漏洞，但**无法保证响应时效或修复周期**。对于低风险问题，我们可能无法及时响应，敬请谅解。

## 报告漏洞

Please report vulnerabilities via [GitHub Security Advisories](https://github.com/zblogcn/zblogphp/security).

请通过 [GitHub Security Advisories](https://github.com/zblogcn/zblogphp/security) 提交报告。

**Pre-submission Checklist:**
1. Please confirm the issue is **not** caused by third-party themes, plugins, or unofficial modifications. If it affects a third-party component, please contact the respective developer.
2. We **do not accept** vulnerabilities requiring Administrator privileges (e.g., Backend RCE, XSS triggered only by admins), as these fall under normal feature authorization.
3. We generally **do not accept** Informational or Low-risk findings (e.g., Self-XSS, negligible CSRF, or issues requiring highly obscure non-default configurations).

**提交前请确认：**
1. 该漏洞**非**第三方主题、插件或非官方代码修改导致。若涉及第三方组件，请直接联系对应开发者。
2. 我们**不接收**需要管理员权限的后台逻辑漏洞（包括但不限于：后台 RCE、需管理员操作触发的 XSS 等），此类行为属于正常的权限范畴。
3. 我们**通常不接受**低风险或信息类漏洞（如 Self-XSS、影响甚微的 CSRF、需要极其特殊的非默认配置才能触发的漏洞）。

**Please include:**
- A detailed description of the vulnerability.
- Steps to reproduce (Proof of Concept). *Please test on a clean installation without third-party themes/plugins.*
- Any affected version(s) or configuration(s).

**报告内容请包含：**
- 漏洞的详细描述。
- 复现步骤（PoC）。请确保在**纯净安装**（无第三方主题/插件）的环境下进行测试。
- 受影响的版本或配置。

**致谢**

Valid reports submitted via GHSA will be eligible for a **CVE ID** and credit in our security advisory.

通过 GHSA 机制提交并经确认为有效的漏洞，我们将为其申请 CVE 编号并在安全公告中致谢（Credit）。
