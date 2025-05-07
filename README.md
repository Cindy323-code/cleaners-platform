# C2C 家政服务平台 (Cleaners Platform)

一个连接家庭主人与清洁服务提供者的在线平台，基于PHP和BCE（Boundary-Control-Entity）架构开发。

## 项目概述

本平台旨在为家庭主人提供寻找清洁工的便捷方式，同时为清洁工提供一个展示服务和找到客户的平台。系统支持多种用户角色（家庭主人、清洁工、平台管理员）和丰富的功能，包括搜索服务、添加收藏、创建服务等。

## 环境要求

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.2+
- Web服务器（Apache/Nginx）
- (可选) Node.js 16+（如需使用前端Vite项目）

## 部署指南

### 1. 部署后端（PHP）

#### 使用XAMPP（Windows推荐）

1. 下载并安装 [XAMPP](https://www.apachefriends.org/)

2. 复制项目文件:
   - 将整个 `Cleanplatform` 文件夹复制到 `C:\xampp\htdocs\` 目录下
   ```
   xcopy /E /I "项目路径\src\Cleanplatform" "C:\xampp\htdocs\Cleanplatform"
   ```

3. 启动服务：
   - 打开XAMPP控制面板
   - 启动Apache和MySQL服务

4. 设置数据库：
   - 打开浏览器访问 http://localhost/phpmyadmin
   - 创建一个名为 `CleanPlatform` 的数据库
   - 导入数据库脚本：
     - 导入 `src/Cleanplatform/config/test.sql` 文件

5. 配置数据库连接：
   - 打开 `C:\xampp\htdocs\Cleanplatform\config\Database.php`
   - 根据需要修改数据库连接信息（默认配置适用于标准XAMPP安装）

## 使用指南

### 访问系统

- 后端: 打开浏览器访问 `http://localhost/Cleanplatform/public/index.php`

### 用户角色与功能

#### 1. 家庭主人 (Home Owner)

- 注册/登录系统
- 搜索可用的清洁工服务
- 收藏喜欢的清洁工
- 查看服务使用历史
- 管理个人资料

**主要入口**:
- 搜索服务: `/boundary/homeowner/search_available_cleaners.php`
- 查看收藏: `/boundary/shortlist/view_shortlist.php`

#### 2. 清洁工 (Cleaner)

- 注册/登录系统
- 创建和管理清洁服务
- 查看自己服务的浏览量和收藏次数
- 查看历史服务匹配记录
- 管理个人资料

**主要入口**:
- 管理服务: `/boundary/service/create_cleaning_service.php`
- 查看统计: `/boundary/service/view_service_shortlist_count.php`

#### 3. 平台管理员 (Platform Manager)

- 管理用户账户
- 管理服务类别
- 查看平台报告（日报/周报/月报）
- 系统监控

**主要入口**:
- 用户管理: `/boundary/admin/search_user_account.php`
- 类别管理: `/boundary/category/search_service_category.php`
- 报告生成: `/boundary/report/generate_daily_report.php`

## 架构说明

本系统采用BCE（Boundary-Control-Entity）架构:

- **Boundary**: 用户界面层，处理用户输入和显示
- **Control**: 控制层，实现业务逻辑
- **Entity**: 实体层，负责数据持久化

```
Cleanplatform/
├── boundary/        # 用户界面层
├── controller/      # 控制层
├── entity/          # 实体层
├── config/          # 配置文件
└── public/          # Web入口
```

## 问题排查

1. **数据库连接问题**
   - 检查 `Database.php` 中的连接参数
   - 确认MySQL服务已启动
   - 默认端口为3307，如果使用3306需修改配置

2. **页面404错误**
   - 确认项目文件夹已正确复制到htdocs目录
   - 检查Apache配置和重写规则
   - 访问路径是否正确（区分大小写）

3. **权限问题**
   - 确保web服务器有读写项目文件的权限

## 联系与支持

如有问题或建议，请联系项目维护者或提交Issue。
