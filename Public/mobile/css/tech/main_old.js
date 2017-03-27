/**
 * Created by moz on 2014/8/29.
 */


var status_img = {
    "OK": "ic_accept.png",
    "WARNING": "ic_warning_yellow.png",
    "ERROR": "ic_warning_red.png"
}


/**
 * 通用得到显示值的方法
 */
function common_get_val(val) {
    return val
}


/**
 * 检测值对应的状态图片
 */
var status_mapping = {
    "机油液位": {
        getImage: function (val) {
            if (val === "正常") {
                return status_img.OK
            } else if (val === "偏低") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        },
        getSuggestion: function (val) {
            if (val === "正常") {
                return "/"
            } else {
                return "当机油液位处于4/5~1/1时，处于正常范围。偏高容易窜入缸内造成少机油，形成积碳，卡住活塞环增大与缸套的摩擦。偏低会润滑不良促进机件磨损"
            }
        }, getValue: common_get_val
    },
    "空气滤芯": {
        getImage: function (val) {
            if (val === "一般") {
                return status_img.OK
            } else if (val === "脏污") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        },
        getSuggestion: function (val) {
            if (val === "一般") {
                return "/"
            } else {
                return "当空滤上有少许灰尘时，技师会气枪清理干净重新装上；如果有严重污染时，及时更换"
            }
        }, getValue: common_get_val
    },
    "空调滤芯": {
        getImage: function (val) {
            if (val === "一般") {
                return status_img.OK
            } else if (val === "脏污") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        },
        getSuggestion: function (val) {
            if (val === "一般") {
                return "/"
            } else {
                return "建议一年或1.5万公里后更换。"
            }
        }, getValue: common_get_val
    },
    "雨刮水液面": {
        getImage: function (val) {
            if (val === "正常") {
                return status_img.OK
            } else if (val === "偏低") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        },
        getSuggestion: function (val) {
            if (val === "正常") {
                return "/"
            } else {
                return "为了保证车辆行驶中，有充足的雨刮水，每次保养或检测完我们技师会添加至满，建议客户平时也注意添加"
            }
        }, getValue: common_get_val
    },
    "左前胎胎压": {
        getImage: function (val) {
            if (val === "正常") {
                return status_img.OK
            } else if (val === "偏低") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        },
        getSuggestion: function (val) {
            if (val === "正常") {
                return "/"
            } else {
                return "当胎压处于2.2~2.5bar左右时，正常。若有偏低，及时补充至规范气压内；若其中某一轮气压明显偏低，有可能被砸钉子，及时补胎或更换轮胎"
            }
        }, getValue: common_get_val
    },
    "右前胎胎压": {
        getImage: function (val) {
            if (val === "正常") {
                return status_img.OK
            } else if (val === "偏低") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        },
        getSuggestion: function (val) {
            if (val === "正常") {
                return "/"
            } else {
                return "当胎压处于2.2~2.5bar左右时，正常。若有偏低，及时补充至规范气压内；若其中某一轮气压明显偏低，有可能被砸钉子，及时补胎或更换轮胎"
            }
        }, getValue: common_get_val
    },
    "左后胎胎压": {
        getImage: function (val) {
            if (val === "正常") {
                return status_img.OK
            } else if (val === "偏低") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "正常") {
                return "/"
            } else {
                return "当胎压处于2.2~2.5bar左右时，正常。若有偏低，及时补充至规范气压内；若其中某一轮气压明显偏低，有可能被砸钉子，及时补胎或更换轮胎"
            }
        }, getValue: common_get_val
    },
    "右后胎胎压": {
        getImage: function (val) {
            if (val === "正常") {
                return status_img.OK
            } else if (val === "偏低") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "正常") {
                return "/"
            } else {
                return "当胎压处于2.2~2.5bar左右时，正常。若有偏低，及时补充至规范气压内；若其中某一轮气压明显偏低，有可能被砸钉子，及时补胎或更换轮胎"
            }
        }, getValue: common_get_val
    },
    "防冻液液面": {
        getImage: function (val) {
            if (val === "正常") {
                return status_img.OK
            } else if (val === "偏低") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "正常") {
                return "/"
            } else {
                return "当防冻液液面在2/3~1/1时，处于正常状态。若出现大量减少，冷却系统有漏水现象，需到4S店具体检查"
            }
        }, getValue: common_get_val

    },
    "防冻液冰点值": {
        getImage: function (val) {
            return status_img.OK
        }, getSuggestion: function (val) {
//            if (val === "正常") {
            return "/"
//            } else {
//                return "检测防冻液冰点值是否小于室外最低气温，减少防冻液应冰点升高而失效的可能，从而防止冻坏冷却管路"
//            }
        }, getValue: common_get_val
    },
    "制动液含水量": {
        getImage: function (val) {
            if (val === "<3%") {
                return status_img.OK
            } else if (val === "3%~4%") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "<3%") {
                return "/"
            } else {
                return "当刹车油含水量小于3%时，正常；3%~4%时，建议更换；大于4%时，马上更换"
            }
        }, getValue: common_get_val
    },
    '前刹车片厚度': {
        getImage: function (val) {
            if (val === ">3.4mm") {
                return status_img.OK
            } else if (val === "2.7~3.4mm") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === ">3.4mm") {
                return "/"
            } else {
                return "当刹车片厚度大于3.4mm时，正常使用；在2.7mm~3.4mm之间时，建议更换；当小于2.7mm，马上更换"
            }
        }, getValue: common_get_val
    },
    '后刹车片厚度': {
        getImage: function (val) {
            if (val === ">3.4mm") {
                return status_img.OK
            } else if (val === "2.7~3.4mm") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === ">3.4mm") {
                return "/"
            } else {
                return "当刹车片厚度大于3.4mm时，正常使用；在2.7mm~3.4mm之间时，建议更换；当小于2.7mm，马上更换"
            }
        }, getValue: common_get_val
    },
    '前刹车盘厚度': {
        getImage: function (val) {
            if (val === "轻微磨损") {
                return status_img.OK
            } else if (val === "较重磨损") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "轻微磨损") {
                return "/"
            } else {
                return "轻微磨损，正常使用；较重磨损，下次保养时建议更换；严重磨损，马上更换"
            }
        }, getValue: common_get_val
    },
    '后刹车盘厚度': {
        getImage: function (val) {
            if (val === "轻微磨损") {
                return status_img.OK
            } else if (val === "较重磨损") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "轻微磨损") {
                return "/"
            } else {
                return "轻微磨损，正常使用；较重磨损，下次保养时建议更换；严重磨损，马上更换"
            }
        }, getValue: common_get_val
    },
    '蓄电池健康度': {
        getImage: function (val) {
            if (val !== "<50%") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val !== "<50%") {
                return "/"
            } else {
                return "正常蓄电池寿命在2~4年内，通过技师的检测器，会显示剩余寿命，剩余>50%时正常使用、剩余<50%建议下次保养时更换"
            }
        }, getValue: common_get_val
    },
    '前胎花纹深度': {
        getImage: function (val) {
            if (val === ">3.5mm") {
                return status_img.OK
            } else if (val === "2.5~3.5mm") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === ">3.5mm") {
                return "/"
            } else {
                return "当花纹厚度大于3.5mm，正常使用；处于2.5mm~3.5mm时，建议下次保养时更换；若小于2.5mm，及时更换。另外如果有鼓包和裂纹时，也应及时更换"
            }
        }, getValue: common_get_val
    },
    '后胎花纹深度': {
        getImage: function (val) {
            if (val === ">3.5mm") {
                return status_img.OK
            } else if (val === "2.5~3.5mm") {
                return status_img.WARNING
            } else {
                return status_img.ERROR
            }
        },
        getSuggestion: function (val) {
            if (val === ">3.5mm") {
                return "/"
            } else {
                return "当花纹厚度大于3.5mm，正常使用；处于2.5mm~3.5mm时，建议下次保养时更换；若小于2.5mm，及时更换。另外如果有鼓包和裂纹时，也应及时更换"
            }
        }, getValue: common_get_val
    },
    '雨刮片': {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.WARNING
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "根据客户自己的需求，如果感觉雨刮刮不干净时，请及时更换，以免雨天影响视线"
            }
        }, getValue: common_get_val
    },
    '灯光照明': {
        getImage: function (lights) {
            var arr = lights.split(",")
            if (lights.trim() === "" || arr.length == 0) {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        },
        getSuggestion: function (lights) {
            var arr = lights.split(",")
            if (lights.trim() === "" || lights.length == 0) {
                return "/"
            } else {
                return "坏灯：" + lights
            }
        }, getValue: function (lights) {
			if (lights.trim() === "" || lights.length == 0) {
				return "坏灯数0个"
			}
            var arr = lights.split(",")
            if (arr.length > 0) {
                return "坏灯数" + arr.length + "个"
            }
        }
    },
    "球头": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        },
        getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "检查各防尘罩有无破损，有赃物进入，如果出现破损及时去4S店更换"
            }
        }, getValue: common_get_val
    },
    "转向拉杆": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "检查有无形变或松动"
            }
        }, getValue: common_get_val
    },
    "平衡杆": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "检查有无形变或松动"
            }
        }, getValue: common_get_val
    },
    "连接杆": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "检查有无形变或松动"
            }
        }, getValue: common_get_val
    },
    "避震": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "检查避震器及弹簧是否正常，有无形变，松不松。"
            }
        }, getValue: common_get_val
    },
    "上下摆臂": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "检查有无形变或松动"
            }
        }, getValue: common_get_val
    },
    "半轴": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "检查防尘罩是否破损，有漏油"
            }
        }, getValue: common_get_val
    },
    "安全带": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "目测外表是否受损，相应安全功能是否正常"
            }
        }, getValue: common_get_val
    },
    "手制动器": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "在3~4齿左右，手刹制动正常"
            }
        }, getValue: common_get_val
    },
    "发动机舱管线": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "目测没有明显渗漏处，各个接插件接插完好。无线路管路干涉情况"
            }
        }, getValue: common_get_val
    },
    "电瓶指示灯": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "该指示灯用来显示电瓶使用状态。打开钥匙门，车辆开始自检 时，该指示灯点亮。启动后自动 熄灭。如果启动后电瓶指示灯常 亮，说明该电瓶出现了使用问题， 需要更换。"
            }
        }, getValue: common_get_val
    },
    "机油指示灯": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "该指示灯用来显示发动机内机油的压力状况。打开钥匙门，车辆 开始自检时，指示灯点亮，启动 后熄灭。该指示灯常亮，说明该 车发动机机油压力低于规定标准，需要维修。"
            }
        }, getValue: common_get_val
    },
    "水温指示灯": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "该指示灯用来显示发动机内冷却液的温度，钥匙门打开，车辆自检时，会点亮数秒，后熄灭。水 温指示灯常亮，说明冷却液温度超过规定值，需立刻暂停行驶。 水温正常后熄灭。"
            }
        }, getValue: common_get_val
    },
    "ABS指示灯": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "该指示灯用来显示ABS工作状况。当打开钥匙门，车辆自检时，ABS灯会点亮数秒，随后熄灭。如果 未闪亮或者启动后仍不熄灭，表 明ABS出现故障。"
            }
        }, getValue: common_get_val
    },
    "发动机指示灯": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "该指示灯用来显示车辆发动机的工作状况，当打开钥匙门时，车 辆自检时，该指示灯点亮后自动熄灭，如常亮则说明车辆的发动机出现了机械故障，需要维修。"
            }
        }, getValue: common_get_val
    },
    "安全带指示灯": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "该指示灯用来显示安全带是否处于锁止状态，当该灯点亮时，说明安全带没有及时的扣紧。有些 车型会有相应的提示音。当安全 带被及时扣紧后，该指示灯自动熄灭。"
            }
        }, getValue: common_get_val
    },
    "刹车指示灯": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "该指示灯是用来显示车辆刹车盘磨损的状况。一般，该指示灯为熄灭状态，当刹车盘出现故障或 磨损过渡时，该灯点亮，修复后熄灭"
            }
        }, getValue: common_get_val
    },
    "气囊指示灯": {
        getImage: function (val) {
            if (val === "良好") {
                return status_img.OK
            } else {
                return status_img.ERROR
            }
        }, getSuggestion: function (val) {
            if (val === "良好") {
                return "/"
            } else {
                return "该指示灯用来显示安全气囊的工作状态，当打开钥匙门，车辆开始自检时，该指示灯自动点亮数 秒后熄灭，如果常亮，则安全气囊出现故障"
            }
        }, getValue: common_get_val
    }

}




