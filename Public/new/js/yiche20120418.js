var bit_stat_url = "url=" + escape(document.location);
if (document.referrer!=null)  bit_stat_url+= "&refer=" + escape(document.referrer)
document.write("<iframe width=0 height=0 FRAMEBORDER=0 src='http://log.bitauto.com/newsstat/log.aspx?"+bit_stat_url+"'></iframe>");
