jQuery(function($) {
 
        $.extend({
                /**
                 * 调用方法： var timerArr = $.blinkTitle.show();
                 *                        $.blinkTitle.clear(timerArr);
                 */
                blinkTitle : {
                        show : function(num) {        //有新消息时在title处闪烁提示
                                var step=0, _title = document.title;
 
                                var timer = setInterval(function() {
                                        step++;
                                        if (step==3) {step=1};
                                        if (step==1) {document.title='【　　　】'+_title};
                                        if (step==2) {document.title='【有'+num+'张新订单，请注意查看】'+_title};
										if (step==4) {document.title='【有'+num+'张新优惠卷，请注意查看】'+_title};
                                }, 500);
 
                                return [timer, _title];
                        },
 
                        /**
                         * @param timerArr[0], timer标记
                         * @param timerArr[1], 初始的title文本内容
                         */
                        clear : function(timerArr) {        //去除闪烁提示，恢复初始title文本
                                if(timerArr) {
                                        clearInterval(timerArr[0]);        
                                        document.title = timerArr[1];
                                };
                        }
                }
        });
})