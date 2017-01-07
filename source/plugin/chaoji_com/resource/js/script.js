jq(function(){
	// Òþ²ØdiscuzÄ¬ÈÏµÄÍ·²¿
	jq('.floattop').hide();
	jq('#floattop1').show();
	// jq('.floattopempty').hide();
	
	jq('#cpcontainer').append('<div class="sideTopBar">\<div id="clientinfo" class="clients"><a href="javascript:" title="">\u5ba2\u6237\u7aef</a></div>\
	<div class="opinion"><a href="javascript:" onclick="open_dialog(this, \'' + PLUGIN_URL + 'pmod=setting&op=feedback&pageurl=' + encodeURIComponent(location.href) + '\');return false;" data-title="\u610f\u89c1\u53cd\u9988">\u610f\u89c1\u53cd\u9988</a></div>\
	<div class="returnTop"><a href="javascript:" onclick="javascript:scroll(0,0);">\u8fd4\u56de\u9876\u90e8</a></div>\
</div>');
	
	jq.getJSON(PLUGIN_URL + 'pmod=setting&op=data&act=data1&callback=?&formhash=' + FORMHASH, function(data){
		if(data.code == 0){
			var data = data.data;
			if(data){
				jq('#clientinfo').children('a').attr('title', '\u5ba2\u6237\u7aefIP\uff1a' + data.loginip + '\n\u603b\u8d21\u732e\uff1a' + data.points + '\n\u7248\u672c\uff1a' + data.version);
				if(data.isonline){
					jq('#clientinfo').addClass('clientsYes');
				}else{
					jq('#clientinfo').removeClass('clientsYes');
				}
			}else{
				jq('#clientinfo').remove();
			}
		}
	});
	
	
});

function chaojiapp_dropmenu(obj){
	showMenu({'ctrlid':obj.id, 'menuid':obj.id + 'child', 'evt':'mouseover'});
	$(obj.id + 'child').style.top = (parseInt($(obj.id + 'child').style.top) - Math.max(document.body.scrollTop, document.documentElement.scrollTop)) + 'px';
	if(BROWSER.ie > 6 || !BROWSER.ie) {
		$(obj.id + 'child').style.left = (parseInt($(obj.id + 'child').style.left) - Math.max(document.body.scrollLeft, document.documentElement.scrollLeft)) + 'px';
	}
}

// µÃµ½cookie
function chaojiapp_getcookie(name, nounescape) {
	name = cookiepre + name;
	var cookie_start = document.cookie.indexOf(name);
	var cookie_end = document.cookie.indexOf(";", cookie_start);
	if(cookie_start == -1) {
		return '';
	} else {
		var v = document.cookie.substring(cookie_start + name.length + 1, (cookie_end > cookie_start ? cookie_end : document.cookie.length));
		return !nounescape ? unescape(v) : v;
	}
}
// °ÑÊý×Ö×ª»¯³ÉÍòµ¥Î»
function chaoji_formatnumber(num){
	if(num == '-1'){
		return '--';
	}else if(num == '-2'){
		return '100+';
	}
	if(num < 10000){
		return num;
	}else{
		var str1 = parseInt(num / 10000) + '\u4e07';
		var str2 = num % 10000;
		var str =  str1 + (str2 == 0 ? '' : str2);
		return str;
	}
}

// °ÑÈÕÆÚ¸ñÊ½»¯ÑÕÉ«
function chaoji_formatdate(date){
	var w = new Date(date.replace(/-/g, "/")).getDay();
	if(w == '0' || w == '6'){
		return '<span class="red">' + date + '</span>';
	}
	return date;
}

// Êä³ö´øÁ´½ÓµÄÊý¾Ý
function chaoji_render_data(date1, date2, val, link){
	if(val == '0' || val == '-1' || val == '-2') val = '--';
	if(date1 == date2){
		
		return '<a href="' + link + '" target="_blank">' + val + '</a>';
	}else{
		return val;
	}
}

// alexa±¨±íÏÔÊ¾
function chaoji_alexaformat(val){
	if(val == '-1' || val == '-2'){
		return '--';
	}else{
		return val;
	}
}

var legendItemClick = function(allItems, item) {
 var max = 0;
 var min = 0;
 serieItems = allItems;
 if (allItems != undefined) {
	 var count = allItems.length;
	 for (var i = 0; i < count; i++) {
		 if (allItems[i].name != item.name) {
			 allItems[i].hide();
		 }
		 if (allItems[i].visible && allItems[i].name == item.name) {
			 if (allItems[i].dataMax > max) {
				 max = allItems[i].dataMax;
			 }
			 min = allItems[i].dataMin == undefined ? 0 : allItems[i].dataMin;
		 }
	 }
	 if (count > 0) {
		 if (max == 125 || max == 150) {
			 allItems[0].chart.yAxis[0].setExtremes(1, max);
		 }
		 else {
			 if (max <= 5) {
				 max = 5;
				 min = 0;
			 }
			 else {
				 var length = max.toString().length;
				 var minlen = min.toString().length;
				 if (length == 1) {
					 max = max <= 5 ? 5 : 10;
					 min = 0;
				 }
				 else {
					 var halfValue = parseInt(padright("5", length - 1, "0"));
					 var addValue = parseInt(padright("1", length, "0"));
					 var maxFullValue = parseInt(padright(max.toString().substring(0, 1), length, "0"));
					 var minFullValue = parseInt(padright(min.toString().substring(0, 1), minlen, "0"));
					 if (max <= halfValue + maxFullValue) {
						 max = halfValue + maxFullValue;
					 }
					 else {
						 max = addValue + maxFullValue;
					 }
					 length = max.toString().length;
					 if (minlen == length) {
						 if (min <= halfValue + minFullValue) {
							 min = minFullValue;
						 }
						 else {
							 min = halfValue + minFullValue;
						 }
					 }
					 else {
						 min = 0;
					 }
				 }
			 }
			 var interval = (max - min) / 5;
			 if (interval.toString().length >= minlen) {
				 min = 0;
				 interval = (max - min) / 5;
			 }
			 else {
				 var mod = min % interval;
				 if (mod != 0) {
					 interval += mod % 5 == 0 ? mod / 5 : mod / 5 + 1;
					 min = min - min % interval;
					 max = min + 5 * interval;
				 }
			 }
			 allItems[0].chart.yAxis[0].options.startOnTick = false;
			 allItems[0].chart.yAxis[0].options.endOnTick = false;
			 allItems[0].chart.yAxis[0].options.tickInterval = interval;
			 allItems[0].chart.yAxis[0].setExtremes(min, max);
		 }
	 }
 }
}

function test_get_min(data){
	var max = 0;
	var min = 0;

		//×îÐ¡Öµ
		Array.prototype.min = function () {
			var min = this[0];
			var len = this.length;
			for (var i = 1; i < len; i++) {
				if (this[i] < min) {
					min = this[i];
				}
			}
			return min;
		}
		//×î´óÖµ
		Array.prototype.max = function () {
			var max = this[0];
			var len = this.length;
			for (var i = 1; i < len; i++) {
				if (this[i] > max) {
					max = this[i];
				}
			}
			return max;
		}
		
		if (data.max() > max) {
			max = data.max();
		}
		
		min = data.min() == undefined ? 0 : data.min();
			
		if (max == 125 || max == 150) {
			//allItems[0].chart.yAxis[0].setExtremes(1, max);
		}
		else {
			if (max < 10) {
				max = max <= 5 ? 5 : 10;
				min = 0;
			}
			else {
				var maxlen = max.toString().length;
				var halfValue = parseInt(padright("5", maxlen - 1, "0"));
				var maxFullValue = parseInt(padright(max.toString().substring(0, 1), maxlen, "0"));
				var newMax = halfValue + maxFullValue;
				if (max <= newMax) {
					max = newMax;
				}
				else {
					var addValue = parseInt(padright("1", maxlen, "0"));
					max = addValue + maxFullValue;
				}
				maxlen = max.toString().length;
				var minlen = min.toString().length;
				if (minlen == maxlen) {
					var minFullValue = parseInt(padright(min.toString().substring(0, 1), minlen, "0"));
					if (min <= halfValue + minFullValue) {
						min = minFullValue;
					}
					else {
						min = halfValue + minFullValue;
					}
				}
				else {
					min = 0;
				}
			}
			var interval = (max - min) / 5;
			if (interval.toString().length >= minlen) {
				min = 0;
				interval = (max - min) / 5;
			}
			else {
				var mod = min % interval;
				if (mod != 0) {
					interval += mod % 5 == 0 ? mod / 5 : mod / 5 + 1;
					min = min - min % interval;
					max = min + 5 * interval;
				}
			}
			//allItems[0].chart.yAxis[0].update({ tickInterval: interval, min: min, max: max });
		}
		
		var robj = {};
		robj.tickInterval = interval;
		robj.min = min;
		robj.max = max;
		return robj;
	
}
												 
function padright(firstChar, length, addChar) {
 for (var i = 0; i < length - 1; i++) {
	 firstChar += addChar;
 }
 return firstChar;
}

    function showOneSerie(chart) {
        var length = chart.series.length;
        for (var i = 0; i < length; i++) {
            chart.series[i].hide();
        }
        chart.series[0].show();
    }

function InitKeywordChartNew(Data, keywordID, searchDate, keyword, bgcolor) {
    var chart;
    // Data.data[0].color = "#4572A7";
    jq('#container' + keywordID + searchDate).highcharts({
        chart: {
            height: 150,
            defaultSeriesType: 'spline',
            //width: 618,
            backgroundColor: bgcolor
        },
        credits: {
            enabled: false
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: Data.categories,
            tickInterval: (Data.categories.length / 24 < 1) ? 1 : (parseInt(Data.categories.length / 24) * 2),
            labels: {
                style: {
                    align: 'right',
                    width: '80px',
                    color: '#666',
                    font: 'normal 12px ËÎÌå'
                },
                x: 10,
                y: 18
            }
        },
        yAxis: {
            min: 1,
            max: Data.max,
            title: {
                text: ''
            },
            labels: {
                formatter: function () {
                    if (this.value == 125) {
                        return "100+";
                    }
                    else if (this.value == 150) {
                        return "\u65e0\u7ed3\u679c";
                    }
                    return (this.value == 0) ? 1 : (this.value)
                },
                style: { font: 'normal 12px ËÎÌå', width: '20px' }
            },
            tickPixelInterval: 30,
            reversed: true
        },
        tooltip: {
            formatter: function () {
                var s = '\u5173\u952e\u8bcd\u3010' + keyword + '\u3011';

                jQuery.each(this.points, function (i, point) {
                    var y = point.y;
                    if (y == 125) {
                        y = "100\u540d\u5916";
                    }
                    else if (y == 150) {
                        y = "\u67e5\u8be2\u65e0\u7ed3\u679c";
                    }
                    s += '<br/><span style="color:' + point.series.color + '">' + point.series.name + '</span>: ' + y;
                });
                s += '<br/>\u66f4\u65b0\u65f6\u95f4\uff08<span style="color:red;">' + Data.dates[this.points[0].point.x] + '</span>\uff09';
                return s;
            },
            shared: true,
            crosshairs: true
        },

        legend: {
            enabled: false
        },

        series: Data.data
    });
    if (Data.nodata) {
        jq('#container' + keywordID + searchDate).append(" <div class='chart-empty'>\u672a\u627e\u5230\u8be5\u65f6\u6bb5\u5ba2\u6237\u7aef\u76d1\u63a7\u6570\u636e</div>");
    }
}


jq(function () {
    // Êó±ê¾­¹ýÆøÅÝ
    jq('div.unknown_box').live('hover', function (event) {
		if(event.type=='mouseenter'){ 
			jq(this).children('.unknown_icon').show().next().show();
		}else{
			jq(this).children('.unknown_icon').hide().next().hide();
		}
    });
})	

function setKeywordDetal() {
    jq("#mos_hover").find(".seo-general-keyword-detail").hide();
    jq("#mos_hover").find(".seo-general-keyword-summary").each(function () {
        var spanItem = jq(this).find("td.change").find("span:first");
        if (spanItem.attr("old") != undefined) {
            spanItem.attr("class", spanItem.attr("old")).removeAttr("old");
        }
    });
}

function setKeywordDetail(obj) {
    jq("#mos_hover").find(".seo-general-keyword-detail").hide();
    jq("#mos_hover").find(".seo-general-keyword-summary").each(function () {
        var spanItem = jq(this).find("td.change").find("span:first");
        if (spanItem.attr("old") != undefined) {
            spanItem.attr("class", spanItem.attr("old")).removeAttr("old");
        }
    });
    if (obj == 0) {
        jq("#list-seo-general-keyword-detail tr").removeClass('even');
        jq("#list-seo-general-keyword-detail tr.seo-general-keyword-summary:even").addClass('even');
        jq("#list-seo-general-keyword-detail tr.seo-general-keyword-detail:even").addClass('even');
    }
}

jq(function(){
	
	jq("a.sort").live("click", function () {
		var _target = jq(this);
		_target.parent().parent().siblings().find("a.sort").removeClass().addClass("sort");
		if (_target.hasClass("sort-desc")) {
			_target.removeClass().addClass("sort sort-asc");
		}
		else {
			_target.removeClass().addClass("sort sort-desc");
		}
		_target.find("span.inner").css('padding-right', '8px');
	});
	
	if(jq(document.body).find('#mos_hover').length){
		TableOrderOper.Init("mos_hover", 0, {OnShow: function (i, trJqObj, _tbodyObj) {}});
		TableOrderOper.SetOrder("BaiduIndex", 1, { DataType: "int", ValAttr:"_order", OnClick: function () {setKeywordDetail(0);} });
		TableOrderOper.SetOrder("Bids", 2, { DataType: "int", ValAttr:"_order", OnClick: function () { setKeywordDetail(0); } });
		TableOrderOper.SetOrder("BaiduRank", 3, { DataType: "int", ValAttr:"_order", OnClick: function () {setKeywordDetail(0); }});
		
		TableOrderOper.SetOrder("GoogleRank", 4, { DataType: "int", ValAttr:"_order", OnClick: function () { setKeywordDetail(0); } });
		TableOrderOper.SetOrder("ChangWeici", 5, { DataType: "int", ValAttr:"_order", OnClick: function () { setKeywordDetail(0); } });
		TableOrderOper.SetOrder("Frequency", 6, { DataType: "int", ValAttr:"_order", OnClick: function () {setKeywordDetail(0);}});
		TableOrderOper.SetOrder("Density", 7, { DataType: "int", ValAttr:"_order", OnClick: function () { setKeywordDetail(0); } });
	}
});

// µ¼³öÊý¾Ý
function export_data(tid, url){
	jq.get(url, {tempdata : jq('#' + tid).val()}, function(data){
			
	});
}

function chaoji_alexatrendformat(val){
	if(parseInt(val) > 0){
		return '<font color="red">' + Math.abs(val) + '</font> <img src="source/plugin/chaoji_com/resource/images/arrow-down.png" title="\u51cf\u5c11' + Math.abs(val) + '" />';
	}else if(parseInt(val) < 0){
		return '<font color="green">' + Math.abs(val) + '</font> <img src="source/plugin/chaoji_com/resource/images/arrow-up.png" title="\u589e\u52a0' + Math.abs(val) + '" />';
	}else{
		return val;
	}
}

function chaoji_dataformat(data){
	if(data == '-1' || data == '-2' || data == '--') data = 'null';
	return data;
}

function chaoji_array_reverse(s){
	var t=[];
	var arr = [];
	for(var itm in s){
		arr.push({date:itm, data:s[itm]});
	}
	var sorted = arr.sort(function(a, b) {
	  return a.date > b.date ? 1 : a.date < b.date ? -1 : 0; ;
	});
	
	jq.each(sorted, function(i, n){
		t[n.date] = n.data;
	});

	return t;
}

function chaojiapp_SetScrollBarTop(_target,length) {
    var cur;
    if (_target.parent().prevAll().find("span").hasClass("unfold_show")) {
        cur = _target.offset().top - 150 - length;
    }
    else {
        cur = _target.offset().top;
    }
	if(jq(document).find('tr.slide_tr').length){
		if(cur < jq('tr.slide_tr').position().top + 105) cur = cur +155;
		jq("html,body").animate({ scrollTop: cur - 250}, 1000);
	}else{
		jq("html,body").animate({ scrollTop: cur -60}, 1000);
	}
}


function initColorSelector(b, c) {
	function d(i) {
		if (g) {
			var j;
			if (g && g.childNodes && g.childNodes.length > 0)
				j = g.childNodes[0];
			if (j)
				j.style.backgroundColor = i
		}
	}
	function e() {
		if (initColorSelector.inited)
			f();
		else {
			initColorSelector.inited = 1;
			addHandler(document.documentElement, "mousedown", f)
		}
		var i = h.value.replace("#", "");
		if (i == "")
			i = "ffffff";
		var j = root + "/template/default/scripts/colorboard.html?color=" + i;
		window.setTimeout(function () {
			window.colorPanel = openPanel(j, h, "", 240, 240, "auto", function (k) {
					h.value = k;
					d(k)
				});
			window.colorPanel.focus();
		}, 50)
	}
	function f() {
		if (window.colorPanel != null) {
			window.colorPanel.setDestoryBk(false);
			window.colorPanel.close();
		}
		window.colorPanel = null;
	}
	var g;
	if (c)
		g = max.$(c);
	var h = typeof b == "string" ? max.$(b) : b;
	b = h.value;
	if (b == "")
		b = "ffffff";
	else {
		d(b);
		b = b.replace("#", "")
	}
	b = parseInt(b, 16);
	b = to16(16777215^b);
	if (!h.readOnly) {
		h.readOnly = true;
		addHandler(h, "click", e);
		g && addHandler(g, "click", e)
	}
};

function open_dialog(obj, url){
	var datatype = jq(obj).attr('data-type');
	var title = jq(obj).attr('data-title');
	art.dialog.open(url,
	{
		title:title,
		id:'dialog1',
		left:320, 
		//width:422,
		padding:'0px 0px',
		lock:true, 
		fixed:true
	});
}

function GetDateDiff(endDate, startDate) {
    if (endDate.indexOf('0001') > -1) {
        return 0;
    }
    var startTime = new Date(Date.parse(startDate.replace(/-/g, "/"))).getTime();
    var endTime = new Date(Date.parse(endDate.replace(/-/g, "/"))).getTime();
    var dates = Math.abs((startTime - endTime)) / (1000 * 60 * 60 * 24);
    return dates;
}

function curDateTime(stdate, m, servicecycleType) {
    var d;
    if (stdate != null) {
        var arr1 = stdate.substring(0, 11).split('-');
        d = new Date();
		d.setFullYear(arr1[0], arr1[1]-1, arr1[2]);
    }
    else {
        d = new Date();
    }
    if (servicecycleType == undefined || servicecycleType == 0) {
        d.setDate(d.getDate() + parseInt(m) * 31);
    }
    else if (servicecycleType != undefined || servicecycleType == 1) {
        d.setDate(d.getDate() + parseInt(m));
    }
    else {
        d.setDate(d.getDate() + parseInt(m));
    }
    var year = d.getFullYear();
    var month = d.getMonth() + 1;
    var ddate = d.getDate();
    var curDateTime = year;
    if (month > 9) {
        curDateTime = curDateTime + "-" + month;
    }
    else {
        curDateTime = curDateTime + "-0" + month;
    }
    if (ddate > 9) {
        curDateTime = curDateTime + "-" + ddate;
    }
    else {
        curDateTime = curDateTime + "-0" + ddate;
    }
    curDateTime = curDateTime + " 00:00:00";
    return curDateTime;
}

function initPayment(balance) {
    var payments = document.getElementsByName("payment[]");
    for (i = 0; i < payments.length; i++) {
        payments[i].onclick = function () {
            if (this.value == 0) {
                jq("#payment_content").html("\u8d26\u6237\u4f59\u989d " + balance + " \u5143 <a href=\"/user/recharge.aspx\" target=\"_blank\">\u5728\u7ebf\u5145\u503c</a>");
            }
            else {
                jq("#payment_content").html("\u652f\u4ed8\u6e20\u9053\uff1a\u8d22\u4ed8\u901a");
            }
        }
    }
}

//¸ü¸ÄÏÔÊ¾Ì×²ÍÏêÇé
function changePackageDetail(id) {
    var tabs = document.getElementById("subdiv").getElementsByTagName("div");
    for (var i = 0; i < tabs.length; i++) {
        if (tabs[i].id == ("packageDetail_" + id)) {
            jq('#' + tabs[i].id).show();
            selPackagePrice = parseInt(jq("#packageprice_" + id).html());
            jq("#paymoney").html(selOpenMonth * selPackagePrice);
        }
        else {
            jq('#' + tabs[i].id).hide();
        }
    }
}

function ForDight(Dight, How) {
    Dight = Math.round(Dight * Math.pow(10, How)) / Math.pow(10, How);
    return Dight;
}

//»ñÈ¡µ½ÆÚÊ±¼ä
function getExpiredDate(strdate, month, isUseVoucher, isSEOPreference, PreferenceLimitMonth, PreferenceMonth,useRedPacket, servicecycleType) {
    if (servicecycleType == undefined || !isNaN(servicecycleType) && servicecycleType == 0 || isNaN(servicecycleType)) {
        jq("#div_pay_openmonth").show();
    }
    else {
        jq("#div_pay_openmonth").hide();
    }
    if (month == PreferenceLimitMonth && (!isUseVoucher) && isSEOPreference && (servicecycleType == undefined || servicecycleType == 0) && useRedPacket != undefined && !useRedPacket) {
        month += parseInt(PreferenceMonth);
        jq("#expireddate").html(curDateTime(strdate, month, servicecycleType) + " (\u5df2\u5305\u542b\u8d60\u9001" + PreferenceMonth + "\u4e2a\u6708)");
    }
    else {
        jq("#expireddate").html(curDateTime(strdate, month, servicecycleType));
    }
}

//**window¹ö¶¯**//
jq(window).scroll(function() //¾ºÕùÍøÕ¾±í¸ñµÚÒ»ÁÐ¹Ì¶¨Ð§¹û
{	
	var _this = jq(this);
	var left = _this.scrollLeft();
	var h = jq(window).height();
	var top = _this.scrollTop();
	var l = 20;//ÈÝÆ÷¾àÀë¸¸ÈÝÆ÷×ó²à¾àÀë
	var w = 138;//×ó²àµÚÒ»ÁÐ¿í¶ÈÖµ
	if(left>=l)
	{
		jq('.j-zebra .datarival-item').css({'position':'absolute','width':w,'left':left-25,'background':'#f8f8f8','line-height':'26px'});
		jq('td.colname').each(function()
		{
			jq(this).find('div').css({'left':left-25});
		});
	}
	else
	{
		jq('.j-zebra .datarival-item').css({'position':'relative','left':0,'background':'none','line-height':'initial'});	
	}

    //返回顶部显隐
	function returnTop()
    {
        if(top>=h)
        {
            jq('.returnTop').fadeIn(600);    
        }
        else
        {
            jq('.returnTop').fadeOut(600);  
        }
    };
    returnTop();
});
