//app.js
App({
  onLaunch: function () {
    // 展示本地存储能力
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)

    // 登录
    wx.login({
      success: function (login) {
        wx.getUserInfo({
          success: function (res) {
            //console.log(res);
            
            wx.request({
              url: 'https://app.interview-wechat.com/login',
              method: 'get',
              data: {
                code: login.code,
                rawData: res.rawData,
                signature: res.signature,
                encryptedData: res.encryptedData,
                iv: res.iv,
                phone:13638610751
              },
              dataType: 'json',
              success: function (res) {
                console.log(res);
              }
            })
          }
        })
      }
    })

    wx.connectSocket({
      url: "wss://swoole.example.com?uid=1&token=800e12e8cace8c6840938e77753e8f6e"
    });
    wx.onSocketOpen(function (res) {
      console.log('WebSocket连接已打开！');
      console.log(res);

      var params = {
        "content": 'abcdefg',
        "event": "alertTip",
        "toUid": 2
      };

      params = JSON.stringify(params);
      wx.sendSocketMessage({//发送测试数据
        data: params,
        complete: function (res) {
          console.log("sendSocketMessage");
          console.log(res);
        },
        success:function(res){
          console.log('-----zd');
          console.log(res);
        }
      });
    });
    wx.onSocketError(function (res) {
      console.log('WebSocket连接打开失败，请检查！');
      console.log(res);
    });

    wx.onSocketMessage(function (res) {
      console.log('收到服务器内容：' + res.data);
      console.log(res);
    });

    // 获取用户信息
    wx.getSetting({
      success: res => {
        if (res.authSetting['scope.userInfo']) {
          // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
          wx.getUserInfo({
            success: res => {
              // 可以将 res 发送给后台解码出 unionId
              this.globalData.userInfo = res.userInfo

              // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
              // 所以此处加入 callback 以防止这种情况
              if (this.userInfoReadyCallback) {
                this.userInfoReadyCallback(res)
              }
            }
          })
        }
      }
    })
  },
  globalData: {
    userInfo: null
  }
})