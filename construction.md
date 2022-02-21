## Physlet API Construction
 注释：
 1. `g`代表`get`方法，`p`代表`post`方法
 2. 加`*`的代表无需登录
 3. 所有API的返回数据`output`可以在前端使用`response.data.data`获取，状态码与状态信息为`response.data.code`和`response.data.message`
 4. 外面加`[]`表示一个数组，返回数据类型为数组的API会在
 5. `uid`代表用户id，`sid`代表模拟id，`cid`代表种类id，`coid`代表评论id

### 小头像
* **注册/register\* (p):**
  input: `username, email, number, password`
  output: `uid, uname`
* **登录/login\* (p):**
  input: `email_or_username, password`
  output: `uid, uname`
* **登出/logout (g):**
  output: `null`
* **修改密码/changePassword (p):**
  input: `ori_password, password`
  output: `uid, uname`
* **我的信息/myInfo (g):**
  output: `uid, uname, avatar(一个url), sims(模拟的数量), likes(模拟的点赞总数)`
* **修改信息/changeInfo (p)**
  input: `username, number`
  output: `uid, uname`
* **上传头像/uploadAvatar (p)**
  input: `image`
* **检查登录/checkLogin\* (g)**
  output: `null`

### 主页
* **推荐模拟/getSims\* (p):**
  input: `opt(可选，如果空或0则默认前10个，1代表10-20个，2代表20-30)`
  output: `[sid, sname, cid, cname, synopsis, likes, uid, uname, create_time]`
* **搜索模拟/search\* (p)**
  input: `key, opt(可选，如果空则默认前10个，1代表10-20个，2代表20-30)`
  output: `[sid, sname, cid, cname, synopsis, likes, uid, uname, create_time]`
* **筛选模拟/filter\* (p)**
  input: `cid, opt(可选，如果空或0则默认前10个，1代表10-20个，2代表20-30)`
  output: `[sid, sname, cid, cname, synopsis, likes, uid, uname, create_time]`
* **所有类别/getCats\* (g)**
  output: `[cid, cname]`

### 模拟页面
* **模拟信息/getSim\* (p)**
  input: `sid`
  output: `sid, sname, cid, cname, synopsis, likes, uid, uname, url, create_time`
* **获取评论/getComs\* (p)**
  input: `sid, opt(可选，如果空或0则默认前10个，1代表10-20个，2代表20-30)`
  output: `[coid, create_time, content, uid, uname, avatar, sid, pid(上一级评论，如果是0，则代表是主评论)]`
* **发表评论/sendCom (p)**
  input: `sid, content, pid(可选)`
  output: `null`
* **删除评论/deleteCom (p)**
  input: `coid`
  output: `null`
* **是否已赞/checkLike (p)**
  input: `sid`
  output: `like(true or false, 如果评分则-1代表未评，0-5代表评分)`
* **点赞与否/like (p 如果已经点赞就取消，未点赞就点赞)**
  input: `sid`
  output: `null`
* **下载模拟/download (p)**
  input: `sid`
  output: `file`

### 个人页面
* **获取自己模拟/getMySims (p)**
  output: `[sid, sname, cid, cname, synopsis, likes, uid, uname, access, create_time]`

### 他人页面
* **用户信息/userInfo\* (p)**
  input: `uid`
  output: `uid, uname, avatar(一个url), sims(模拟的数量), likes(模拟的点赞总数)`
* **获取该用户模拟/getHisSims\*  (p)**
  input: `uid`
  output: `[sid, sname, cid, cname, synopsis, uid, uname, likes, create_time]`
* **关注与否/follow (g)**
  input: `uid`
  output: `null`

### 编辑页面
* **编辑模拟/editSim (p)**
  input: `sid, sname, cid, synopsis, access, file`
* **删除模拟/deleteSim (p)**
  input: `sid`

### 上传页面
* **上传模拟/uploadSim (p)**
  input: `sname, cid, synopsis, access, file`
  output: `sid, url`

### 用户列表
* **关注列表/followingList (p)**
  input: `uid`
  ouput: `[uid, uname, avatar(一个url), sims(模拟的数量), likes(模拟的点赞总数)]`
* **粉丝列表/followerList (p)**
  input: `uid`
  ouput: `[uid, uname, avatar(一个url), sims(模拟的数量), likes(模拟的点赞总数)]`
* **搜索用户/searchUser (p)**
  input: `key`
  output: `[uid, uname, avatar(一个url), sims(模拟的数量), likes(模拟的点赞总数)]`

### 消息列表
* **获取信息/messageList (g)**
  output: `[class(1代表系统消息，2代表点赞信息，3代表评论信息，4代表回复评论信息，5代表私信), uid(谁进行的点赞或评论), uname, sid(在哪个模拟下点赞或评论), sname, coid(仅class=3时存在), content(class=1时不存在), create_time]`
