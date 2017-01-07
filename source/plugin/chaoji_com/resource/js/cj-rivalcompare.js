/*=====================================
* Usability
*=====================================*/
jq(function () {
    //表格样式
    jq('tbody.j-zebra').each(function () {
        jq(this).find('tr:odd').addClass('even');
        jq(this).find('tr').hover(function () {
            jq(this).addClass('hover');
        }, function () {
            jq(this).removeClass('hover');
        }).click(function () {
            jq(this).parents('table').find('tr').removeClass('selected');
            jq(this).addClass('selected');
        });
    });

    //表头跟随滚动
    (function () {
        if (!document.getElementById('table-rivalsites')) return false;
        var rivalsitesTop = jq('#table-rivalsites').parent().offset().top;
        jq(window).scroll(function () {
            var _scrollTop = document.documentElement.scrollTop + document.body.scrollTop;
            if (_scrollTop > rivalsitesTop) {
                jq('#table-rivalsites').css('top', (_scrollTop - rivalsitesTop + 47) + 'px');
            } else {
                jq('#table-rivalsites').css('top', '0');
            }
        });
        jq(window).resize(function () {
            jq(window).scroll();
        });
    })();

    //选择对比站点

    jq('#j-rival-sites1 a').click(function () {
        var _s = jq(this).parent().prevAll().length + 2;

        var _n = 5;

        if (document.getElementById('rivalcompare-wide-label')) {
            _n = 7;
        }

        if (jq(this).is('.active')) {
            jq('#table-rivalsites tr').each(function () {
                jq(this).find('th').eq(_s).hide().addClass('j-hide');
                if (jq(this).find('th').not('.j-hide').length < _n) {
                    jq(this).append('<th class="datarival-site j-blank"><div class="clearfix box"></div></th>');
                }
            });
            jq('#table-rivaldata tr').each(function () {
                var _td = jq(this).find('td');
                if (_td.length != 1) {
                    _td.eq(_s).hide().addClass('j-hide');
                    if (_td.not('.j-hide').length < _n) {
                        jq(this).append('<td class="datarival-site j-blank"><div class="clearfix box"></div></td>');
                    }
                } else if (Number(_td.attr('colspan')) > _n) {
                    _td.attr('colspan', Number(_td.attr('colspan')) - 1);
                }
            });
            jq(this).removeClass('active');
        } else {
            jq('#table-rivalsites tr').each(function () {
                jq(this).find('th').eq(_s).show().removeClass('j-hide');
                if (jq(this).find('th').not('.j-hide').length > _n && jq(this).children().last().is('.j-blank')) {
                    jq(this).children().last().remove();
                }
            });
            jq('#table-rivaldata tr').each(function () {
                var _td = jq(this).find('td');
                if (_td.length != 1) {
                    _td.eq(_s).show().removeClass('j-hide');
                    if (_td.not('.j-hide').length > _n && jq(this).children().last().is('.j-blank')) {
                        jq(this).children().last().remove();
                    }
                } else if (jq('#table-rivalsites th').not('.j-hide').length > _n) {
                    _td.attr('colspan', Number(_td.attr('colspan')) + 1);
                }
            });
            jq(this).addClass('active');
        }

        rivalWidescreenR();

        return false;
    });



    jq('#table-rivalsites a.del').click(function () {
        var _s = jq(this).parent().parent().prevAll().length - 2;
        jq('#j-rival-sites1 a').eq(_s).click();
        return false;
    });

    /*
    jq('#table-rivalsites a.del').click(function(){
    var _s=jq(this).parent().parent().prevAll().length;
    var _table=jq(this).parents('table');
    _table.parent().find('tr').each(function(){
    jq(this).find('th, td').eq(_s).hide();
    var _cols=jq(this).find('th');
    if (_cols.length<5 && _cols.length>0){
    jq(this).append('<th class="datarival-site"><div class="clearfix box"></div></th>');
    }
    var _cols2=jq(this).find('td');
    if (_cols2.length<5 && _cols2.length>0){
    jq(this).append('<td class="datarival-site"><div class="clearfix box"></div></td>');
    }
    });
    jq('#j-rival-sites1 a').eq(_s-2).removeClass('active');
    return false;
    });
    */

    //全屏的处理

    rivalWidescreenR();

    function rivalWidescreen() {
        if (!document.getElementById('rivalcompare-wide-label')) return false;
        var _left = jq('#rivalcompare-wide-label').parent().parent().offset().left + 1;
        jq(window).scroll(function () {
            var _scroll = document.documentElement.scrollLeft + document.body.scrollLeft;
            if (_scroll > _left) {
                jq('#rivalcompare-wide-label').css('left', (_scroll - _left) + 'px').find('table').css('border-right', '1px solid #DDE3E6');
                jq('#rivalcompare-wide-label').show();
            } else {
                jq('#rivalcompare-wide-label').css('left', '0px').find('table').css('border-right', 'none');
                jq('#rivalcompare-wide-label').hide();
            }
        });
    }
    rivalWidescreen();

    //ie的z-index问题
    (function () {
        if (jq.browser.msie && (jq.browser.version == '6.0' || jq.browser.version == '7.0')) {
            jq('#j-siteswitcher').parents('div.phead').css({
                'position': 'relative',
                'z-index': '21'
            }).next().css({
                'position': 'relative',
                'z-index': '0'
            });
        }
    })();



});

function rivalWidescreenR() {
    if (!document.getElementById('rivalcompare-wide-label')) return false;
	jq('#rivalcompare-wide-data').parent().height(jq('#rivalcompare-wide-data').height() + 34);
    var _label = jq('#table-rivaldata-label tr');
    var _data = jq('#table-rivaldata tr');
    for (var i = 0; i < _label.length; i++) {
        var _height = _data.eq(i).find('td').eq(-1).height();
        _label.eq(i).find('td').height(_height);
    }
}