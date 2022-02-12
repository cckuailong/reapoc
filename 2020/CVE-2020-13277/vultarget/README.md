# CVE-2020-13277

> CVE-2020-13277 靶场： Gitlab 逻辑漏洞 - 任意用户越权访问私有仓库

------




## 0x10 靶场环境

![](https://img.shields.io/badge/Docker-latest-brightgreen.svg) ![](https://img.shields.io/badge/GitlabEE-10.6.0-brightgreen.svg) ![](https://img.shields.io/badge/Runner-latest-brightgreen.svg) ![](https://img.shields.io/badge/Money-much-brightgreen.svg)




## 0x20 目录结构

```
CVE-2020-13277
├── README.md ............... [此 README 说明]
├── imgs .................... [辅助 README 说明的图片]
├── gitlab .................. [Gitlab 容器的挂载目录]
│   ├── Dockerfile .......... [Gitlab 的 Docker 构建文件]
│   ├── config .............. [Gitlab 配置挂载目录]
│   ├── data ................ [Gitlab 数据挂载目录]
│   ├── logs ................ [Gitlab 日志挂载目录]
│   ├── keys ................ [Gitlab 破解 License 存储目录]
│   └── runner .............. [Runner 容器的挂载目录]
├── license ................. [破解 License 的容器构建目录]
│   ├── Dockerfile .......... [License 的 Docker 构建文件]
│   └── license.rb .......... [生成破解 License 的 Ruby 脚本]
├── docker-compose.yml ...... [Docker 的构建配置]
├── keygen.ps1 .............. [Windows: 一键生成破解 License]
├── keygen.sh ............... [Linux:   一键生成破解 License]
├── run.ps1 ................. [Windows: 一键运行 Gitlab 靶场]
├── run.sh .................. [Linux:   一键运行 Gitlab 靶场]
├── register.ps1 ............ [Windows: 一键注册 Runner]
├── register.sh ............. [Linux:   一键注册 Runner]
├── stop.ps1 ................ [Windows: 一键停止 Gitlab 靶场]
└── stop.sh ................. [Linux:   一键停止 Gitlab 靶场]
```



## 0x30 前置说明

<details>
<summary><b>关于靶场 Docker Image 版本的选择依据</b></summary>
<br/>

此漏洞核心主要是利用 [Mirror Repository](https://docs.gitlab.com/ee/user/project/repository/repository_mirroring.html) - 仓库的镜像同步备份功能。

其中 Mirror 的同步方向又分两种：

- Pull：把指定 Repository 内容拉到当前 Repository
- Push：把当前 Repository 内容推到指定 Repository

> 此漏洞利用的是 Pull 方向的 Mirror Repository

要知道，Gitlab 分为 CE（社区免费版）和 EE（企业收费版）两种版本，而 Gitlab [官方也声称](https://gitlab.com/gitlab-org/cves/-/blob/master/2020/CVE-2020-13277.json) 此漏洞会同时影响 CE 和 EE 的以下版本：

- `>=10.6, <12.9.10`
- `>=12.10, <12.10.11`
- `>=13.0, <13.0.6`

但并不意味着这些版本的 [Gitlab Docker Image](https://hub.docker.com/search?q=gitlab) 都可以用于搭建靶场，这是因为：

- CE 版的 Mirror Repository 只有 Push 方向
- EE 版又细分为 Core、Starter、Premium、Ultimate 四个版本，从官方提供的[功能对比表](https://about.gitlab.com/pricing/self-managed/feature-comparison/)可知：只有 Core 版本是不存在 Pull 方向的，而 Gitlab-EE 的 Docker Image 均只提供是 Core 版本


换而言之，要使用 Docker 搭建靶场，只能选用 Gitlab-EE 版，并将其[破解](https://blog.starudream.cn/2020/01/19/6-crack-gitlab/)（壕也可以选择购买 License）以激活 Mirror Repository - Pull 功能。


但即使破解了 Gitlab-EE，无论 `10.x`、 `12.x` 还是 `13.x`，当 Mirror Repository - Pull 的 URL 包含本地路径时，都会报错 `Import url is blocked: Requests to localhost are not allowed`。

虽然通过 `Admin area => Settings => Network => Outbound requests` 设置 `Allow requests to the local network from hooks and services` 后可以配置本地 URL ，但同步镜像时会报错 `2:Fetching remote upstream failed: fatal: unable to access http://127.0.0.1/xxxx/: The requested URL returned error: 301`。换言之只有 Pull Remote Repository 可用。

不过幸好，尽管 `12.x` 和 `13.x` 对本地 URL 的判断方法十分严格，但是在 `10.x` 版本却有应对之法： 在配置 Pull URL 的时候，只需要配置本地设置的 DNS 服务名称即可实现绕过。


综上可知，最终只能采用 [gitlab-ee:10.6.0-ee.0](https://hub.docker.com/layers/gitlab/gitlab-ee/10.6.0-ee.0/images/sha256-1895df14d31443c8642a2564cdc3fad6948a89df09e616376b10d1b1971a75f8?context=explore) 版本的 Docker Image 构建此靶场。


> 其实通过前面描述也可知道此漏洞的利用条件比较苛刻，基本上穷人是很难被这个漏洞影响的

</details>




## 0x40 靶场搭建

### 0x41 构建

- 宿主机预装 docker 和 docker-compose
- 下载本仓库： [git clone https://github.com/lyy289065406/CVE-2020-13277](https://github.com/lyy289065406/CVE-2020-13277)
- 生成破解密钥对： [`./keygen.sh`](keygen.sh) 或 [`./keygen.ps1`](keygen.ps1)
- 构建并运行 Gitlab （确保 80 端口未占用）： [`./run.sh`](run.sh) 或 [`./run.ps1`](run.ps1)
- 约 5 分钟后可从浏览器登录 Gitlab：[http://127.0.0.1](http://127.0.0.1) （首次登录需要重设管理员账号 root 的密码）

### 0x42 破解

前面生成破解密钥对的时候，已经把公钥写入 Gitlab 容器后台了，还需要把私钥通过前端上传到 Gitlab 完成破解：

- 密钥对生成到 [`./gitlab/keys/`](gitlab/keys/) 目录，复制其下 `.gitlab-license` 的内容（私钥）
- 使用 root 用户打开 [http://127.0.0.1/admin/license/new](http://127.0.0.1/admin/license/new) 页面
- 选择 `Enter license key` 并粘贴私钥，点击 `Upload license` 按钮即可完成破解

> 至此 Mirror Repository - Pull 功能已激活

![](imgs/01.png)


### 0x43 出站设置

- 使用 root 用户打开 [http://127.0.0.1/admin/application_settings](http://127.0.0.1/admin/application_settings) 页面
- 找到最底部的 `Outbound requests` 并勾选 `Allow requests to the local network from hooks and services` 保存

> 至此 Mirror Repository - Pull 已支持拉取本地 Repository

![](imgs/02.png)


### 0x44 设置 Runner

- 使用 root 用户打开 [http://127.0.0.1/admin/runners](http://127.0.0.1/admin/runners) 页面
- 找到 registration token 并复制
- 注册 Runner： [`./register.sh $TOKEN`](register.sh) 或 [`./register.ps1 $TOKEN`](register.ps1)

> 至此所有 Repository 都可以使用此 Runner 执行 CI 脚本（Pipeline Jobs）

![](imgs/03.png)






## 0x50 靶场验证

> 验证过程可参考官方的 [Issue](https://gitlab.com/gitlab-org/gitlab/-/issues/220972)，但下面的验证过程会配合此靶场对某些步骤进行微调


### 0x51 预建验证用账号

使用 root 用户打开 [http://127.0.0.1/admin/users](http://127.0.0.1/admin/users) 页面创建 3 个账号：

- `victim`： 受害者账号
- `attacker1`： 攻击者账号1
- `attacker2`： 攻击者账号2

> 创建账号时不能设置初始密码，Gitlab 默认会推送初始密码到所设置的 Email。为方便起见，其实可以随便填 Email，先创建账号，然后再马上编辑该账号，此时即可用 root 设置该账号的初始密码，而不需要通过 Email

![](imgs/04.png)



### 0x52 预建受害者仓库

- 使用 `victim` 账号登录 Gitlab
- 新建仓库 `New Project`：
    - Name： `target`
    - Visibility Level： `Private`
- 在仓库内新建 `README.md` 文件，随意设置其内容为 `mykey is abcxyz`

> 明显 `target` 为 `victim` 的私有仓库，而我们的目标就是利用漏洞获取此仓库的内容

![](imgs/05.png)



### 0x53 创建攻击者的 poc 仓库

- 使用 `attacker1` 账号登录 Gitlab
- 新建仓库 `New Project`：
    - Name： `poc`
    - Visibility Level： `Public`
- 在仓库内新建 `.gitlab-ci.yml` 文件，其内容为：

```
image: "ruby:2.6"

rspec:  
  script:  
    - git clone http://gitlab-ci-token:$CI_JOB_TOKEN@172.168.30.2/victim/target.git
    - cd target
    - ls -lah .  
    - cat README.md
```

【目的】在接下来的操作中，会通过一些手法使得 `victim` 在**不知情**的状况下，使用其权限执行这个 CI 脚本。

> 因为当前靶场未设置证书，所以只能使用 `http` 协议；另外 `172.168.30.2` 是 [`docker-compose.yml`](docker-compose.yml) 为 Gitlab 容器分配的 IP，因为此 CI 脚本最终是会通过 Runner 执行，而当前靶场的 Runner 和 Gitlab 并不是同一个容器，因此要使用 Docker 所分配的 IP 地址


![](imgs/06.png)



### 0x53 创建攻击者的 poc 镜像仓库

- 使用 `attacker2` 账号登录 Gitlab
- 新建分组 `New Group`：
    - Name： `test`
    - Visibility Level： `Public`
- 在 `test` 分组内新建**完全为空**的仓库 `New Project`：
    - Name： `poc`
    - Visibility Level： `Public`

![](imgs/07.png)


配置镜像仓库 `Settings => Repository => Pull from a remote repository`：

- `Mirror repository`： 勾选
- `Git repository URL`： 填写 `http://GITLAB/attacker1/poc`
- `Password`： （留空，Pull 的仓库密级为 Public，不需要密码）
- `Trigger pipelines for mirror updates`： 勾选（镜像同步时触发 CI 脚本执行）

配置成功后，会每 30 分钟检查一次源仓库是否发生变化，若有变化则强制同步。

> `Git repository URL` 指向的就是前面创建的 poc 仓库，之所以不使用 127.0.0.1 是因为 Pull 禁止拉取本地仓库，但是可以利用 DNS 绕过： `GITLAB` 是 [`docker-compose.yml`](docker-compose.yml) 为 Gitlab 容器分配的 hostname，默认会设置到 `/etc/hosts` 中，虽然宿主机无法解析 `GITLAB`，但容器内相当于访问了 `http://127.0.0.1/attacker1/poc`

![](imgs/08.png)


### 0x54 强制转移 poc 镜像仓库的属主给受害者

- 继续使用 `attacker2` 账号
- 打开 [http://127.0.0.1/groups/test/-/group_members](http://127.0.0.1/groups/test/-/group_members) 页面管理 `test` 分组的用户
- 添加 `victim` 用户进分组：
    - `Add new member to test`： `victim`
    - `Permissions`： `Owner`
    - `Expiration date`： （留空，即无限期）
- 点击右上角账号头像 `=> Setting => Account => Delete account` 删除当前账号（`attacker2`）


【目的】 `test` 分组内只有 `victim` 和 `attacker2` 两个 `Owner`，当 `attacker2` 用户被删除之后，`test` 分组及其下所有仓库的所有权会强制转移到 `victim` 名下。而当前 `test` 分组下有一个从 `attacker1/poc` 镜像同步的 `test/poc` 仓库，即相当于把 `test/poc` 仓库强制过继给 `victim` 用户。

![](imgs/09.png)

![](imgs/10.png)


### 0x55 发起攻击

先捋一下到目前为止的现状：

- 攻击者强制且偷偷地为受害者 `victim` 创建了一个 `test/poc` 仓库
- `test/poc` 仓库的内容是从 `attacker1/poc` 仓库镜像同步的（每 30 分钟检查一次是否发生变化需要同步）
- `attacker1/poc` 仓库的内容是由攻击者控制的，包括 CI 脚本 `.gitlab-ci.yml`
- 因 `test/poc` 仓库设置了 `Trigger pipelines for mirror updates`，故每次同步时会执行 CI 脚本
- 因 `test/poc` 仓库的 `Owner` 是 `victim`，故执行 CI 脚本时会使用 `victim` 的权限

![](imgs/12.png)

回顾前面所设置的 CI 脚本 `.gitlab-ci.yml` 内容，此 poc 是在 Runner 中使用 `victim` 的权限访问了其 Private 仓库 `target` 的目录结构和 `README.md`：

```
image: "ruby:2.6"

rspec:  
  script:  
    - git clone http://gitlab-ci-token:$CI_JOB_TOKEN@172.168.30.2/victim/target.git
    - cd target
    - ls -lah .  
    - cat README.md
```

尔后攻击者只需要任意修改 `attacker1/poc` 仓库中一些无关紧要的内容，最坏情况下只需要等 30 分钟，就可以使用 `victim` 的权限执行设定的 CI 脚本。

虽然攻击者没有直接权限访问 Pipeline Jobs 的执行结果，但是只需要调整脚本的输出目标，例如把仓库的内容发送到指定 Email 或 FTP 服务器，即可窃取仓库代码。

![](imgs/11.png)
