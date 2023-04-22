"use strict";


/***************************************** utillib.js *********************************************/
var isInWorker = (typeof WorkerGlobalScope !== 'undefined' && self instanceof WorkerGlobalScope);

function execBoth(funcMain, funcWorker, params) {
    if (!isInWorker && funcMain) {
        return funcMain.apply(this, params || []);
    }
    if (isInWorker && funcWorker) {
        return funcWorker.apply(this, params || []);
    }
    return {};
}

//execute function only in worker
function execWorker(func, params) {
    return execBoth(undefined, func, params);
}

//execute function only in main
function execMain(func, params) {
    return execBoth(func, undefined, params);
}

execWorker(function () {
    self.$ = {
        isArray: Array.isArray || function (obj) {
            return jQuery.type(obj) === "array";
        },
        noop: function () {}
    };
});

execMain(function () {
    var constants = ['CSTIMER_VERSION', 'LANG_SET', 'LANG_STR', 'LANG_CUR', 'OK_LANG', 'CANCEL_LANG', 'RESET_LANG', 'ABOUT_LANG', 'ZOOM_LANG', 'BUTTON_TIME_LIST', 'BUTTON_OPTIONS', 'BUTTON_EXPORT', 'BUTTON_DONATE', 'PROPERTY_SR', 'PROPERTY_USEINS', 'PROPERTY_USEINS_STR', 'PROPERTY_VOICEINS', 'PROPERTY_VOICEINS_STR', 'PROPERTY_VOICEVOL', 'PROPERTY_PHASES', 'PROPERTY_TIMERSIZE', 'PROPERTY_USEMILLI', 'PROPERTY_SMALLADP', 'PROPERTY_SCRSIZE', 'PROPERTY_SCRMONO', 'PROPERTY_SCRLIM', 'PROPERTY_SCRALIGN', 'PROPERTY_SCRALIGN_STR', 'PROPERTY_SCRFAST', 'PROPERTY_SCRKEYM', 'PROPERTY_SCRCLK', 'PROPERTY_SCRCLK_STR', 'PROPERTY_WNDSCR', 'PROPERTY_WNDSTAT', 'PROPERTY_WNDTOOL', 'PROPERTY_WND_STR', 'EXPORT_DATAEXPORT', 'EXPORT_TOFILE', 'EXPORT_FROMFILE', 'EXPORT_TOSERV', 'EXPORT_FROMSERV', 'EXPORT_FROMOTHER', 'EXPORT_USERID', 'EXPORT_INVID', 'EXPORT_ERROR', 'EXPORT_NODATA', 'EXPORT_UPLOADED', 'EXPORT_CODEPROMPT', 'EXPORT_ONLYOPT', 'EXPORT_ACCOUNT', 'EXPORT_LOGINGGL', 'EXPORT_LOGINWCA', 'EXPORT_LOGOUTCFM', 'EXPORT_LOGINAUTHED', 'IMPORT_FINAL_CONFIRM', 'BUTTON_SCRAMBLE', 'BUTTON_TOOLS', 'IMAGE_UNAVAILABLE', 'TOOLS_SELECTFUNC', 'TOOLS_CROSS', 'TOOLS_EOLINE', 'TOOLS_ROUX1', 'TOOLS_222FACE', 'TOOLS_GIIKER', 'TOOLS_IMAGE', 'TOOLS_STATS', 'TOOLS_HUGESTATS', 'TOOLS_DISTRIBUTION', 'TOOLS_TREND', 'TOOLS_METRONOME', 'TOOLS_CFMTIME', 'TOOLS_SOLVERS', 'TOOLS_SYNCSEED', 'TOOLS_SYNCSEED_SEED', 'TOOLS_SYNCSEED_INPUT', 'TOOLS_SYNCSEED_30S', 'TOOLS_SYNCSEED_HELP', 'TOOLS_SYNCSEED_DISABLE', 'TOOLS_SYNCSEED_INPUTA', 'OLCOMP_UPDATELIST', 'OLCOMP_VIEWRESULT', 'OLCOMP_VIEWMYRESULT', 'OLCOMP_START', 'OLCOMP_SUBMIT', 'OLCOMP_SUBMITAS', 'OLCOMP_WCANOTICE', 'OLCOMP_OLCOMP', 'OLCOMP_ANONYM', 'OLCOMP_ME', 'OLCOMP_WCAACCOUNT', 'OLCOMP_ABORT', 'OLCOMP_WITHANONYM', 'PROPERTY_IMGSIZE', 'TIMER_INSPECT', 'TIMER_SOLVE', 'PROPERTY_USEMOUSE', 'PROPERTY_TIMEU', 'PROPERTY_TIMEU_STR', 'PROPERTY_PRETIME', 'PROPERTY_ENTERING', 'PROPERTY_ENTERING_STR', 'PROPERTY_INTUNIT', 'PROPERTY_INTUNIT_STR', 'PROPERTY_COLOR', 'PROPERTY_COLORS', 'PROPERTY_VIEW', 'PROPERTY_VIEW_STR', 'PROPERTY_UIDESIGN', 'PROPERTY_UIDESIGN_STR', 'COLOR_EXPORT', 'COLOR_IMPORT', 'COLOR_FAIL', 'PROPERTY_FONTCOLOR_STR', 'PROPERTY_COLOR_STR', 'PROPERTY_FONT', 'PROPERTY_FONT_STR', 'PROPERTY_FORMAT', 'PROPERTY_USEKSC', 'PROPERTY_NTOOLS', 'PROPERTY_AHIDE', 'SCRAMBLE_LAST', 'SCRAMBLE_NEXT', 'SCRAMBLE_SCRAMBLE', 'SCRAMBLE_LENGTH', 'SCRAMBLE_INPUT', 'PROPERTY_VRCSPEED', 'PROPERTY_VRCMP', 'PROPERTY_VRCMPS', 'PROPERTY_GIIKERVRC', 'PROPERTY_GIISOK_DELAY', 'PROPERTY_GIISOK_DELAYS', 'PROPERTY_GIISOK_KEY', 'PROPERTY_GIISOK_MOVE', 'PROPERTY_GIISOK_MOVES', 'PROPERTY_GIISBEEP', 'PROPERTY_GIIRST', 'PROPERTY_GIIRSTS', 'CONFIRM_GIIRST', 'PROPERTY_GIIAED', 'scrdata', 'SCRAMBLE_NOOBST', 'SCRAMBLE_NOOBSS', 'STATS_CFM_RESET', 'STATS_CFM_DELSS', 'STATS_CFM_DELMUL', 'STATS_CFM_DELETE', 'STATS_COMMENT', 'STATS_REVIEW', 'STATS_DATE', 'STATS_SSSTAT', 'STATS_CURROUND', 'STATS_CURSESSION', 'STATS_CURSPLIT', 'STATS_EXPORTCSV', 'STATS_SSMGR_TITLE', 'STATS_SSMGR_NAME', 'STATS_SSMGR_DETAIL', 'STATS_SSMGR_OPS', 'STATS_SSMGR_ORDER', 'STATS_SSMGR_ODCFM', 'STATS_SSMGR_SORTCFM', 'STATS_ALERTMG', 'STATS_PROMPTSPL', 'STATS_ALERTSPL', 'STATS_AVG', 'STATS_SOLVE', 'STATS_TIME', 'STATS_SESSION', 'STATS_SESSION_NAME', 'STATS_SESSION_NAMEC', 'STATS_STRING', 'STATS_PREC', 'STATS_PREC_STR', 'STATS_TYPELEN', 'STATS_STATCLR', 'STATS_ABSIDX', 'STATS_XSESSION_DATE', 'STATS_XSESSION_NAME', 'STATS_XSESSION_SCR', 'STATS_XSESSION_CALC', 'STATS_RSFORSS', 'PROPERTY_PRINTSCR', 'PROPERTY_PRINTDATE', 'PROPERTY_SUMMARY', 'PROPERTY_IMRENAME', 'PROPERTY_SCR2SS', 'PROPERTY_SS2SCR', 'PROPERTY_SS2PHASES', 'PROPERTY_STATINV', 'PROPERTY_STATAL', 'PROPERTY_STATALU', 'PROPERTY_DELMUL', 'PROPERTY_TOOLSFUNC', 'PROPERTY_TRIM', 'PROPERTY_TRIM_MED', 'PROPERTY_STKHEAD', 'PROPERTY_HIDEFULLSOL', 'PROPERTY_IMPPREV', 'PROPERTY_AUTOEXP', 'PROPERTY_AUTOEXP_OPT', 'PROPERTY_SCRASIZE', 'MODULE_NAMES', 'BGIMAGE_URL', 'BGIMAGE_INVALID', 'BGIMAGE_OPACITY', 'BGIMAGE_IMAGE', 'BGIMAGE_IMAGE_STR', 'SHOW_AVG_LABEL', 'USE_LOGOHINT', 'TOOLS_SCRGEN', 'SCRGEN_NSCR', 'SCRGEN_PRE', 'SCRGEN_GEN'];
    for (var i = 0; i < constants.length; i++) {
        window[constants[i]] = window[constants[i]] || '|||||||||||||||';
    }

    window.requestAnimFrame = (function () {
        return window.requestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.oRequestAnimationFrame ||
                window.msRequestAnimationFrame ||
                function (/* function */ callback, /* DOMElement */ element) {
                    return window.setTimeout(callback, 1000 / 60);
                };
    })();

    // if (!window.localStorage) {
    //     window.localStorage = {};
    // }

    // if (!('properties' in localStorage) && location.protocol != 'https:' && location.hostname != 'localhost' && location.protocol != "file:") {
    //     location.href = 'https:' + location.href.substring(location.protocol.length);
    // }

    if (window.performance && window.performance.now) {
        $.now = function () {
            return Math.floor(window.performance.now());
        };
    }

    $.urlParam = function (name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results == null) {
            return null;
        } else {
            return results[1] || 0;
        }
    };

    $.hashParam = function (name) {
        var results = new RegExp('[#&]' + name + '=([^&#]*)').exec(window.location.hash);
        if (results == null) {
            return null;
        } else {
            return results[1] || 0;
        }
    };

    $.clearUrl = function (name) {
        var results = new RegExp('[\?&](' + name + '=[^&#]*&?)').exec(window.location.href);
        var result = name ?
                location.href.replace(results[1], '').replace(/\?$/, '') :
                location.pathname;
        if (history && history.replaceState) {
            // console.log(114, result);
            history.replaceState(undefined, undefined, result);
        } else {
            // console.log(117, result);
            location.href = result;
        }
    };

    $.clearHash = function (name) {
        var results = new RegExp('[#&](' + name + '=[^&#]*&?)').exec(window.location.href);
        var result = name ?
                location.href.replace(results[1], '').replace(/#$/, '') :
                location.pathname + location.search;
        if (history && history.replaceState) {
            // console.log(result);
            history.replaceState(undefined, undefined, result);
        } else {
            // console.log(result);
            location.href = result;
        }
    };

    $.clipboardCopy = function (value, callback) {
        var textArea = $('<textarea>' + value + '</textarea>').appendTo(document.body);
        textArea.focus().select();
        var succ = false;
        try {
            succ = document.execCommand('copy');
        } catch (err) {
        }
        textArea.remove();
        return succ;
    };

    $.fingerprint = function () {
        var fp_screen = window.screen && [Math.max(screen.height, screen.width), Math.min(screen.height, screen.width), screen.colorDepth].join("x");
        var fp_tzoffset = new Date().getTimezoneOffset();
        var fp_plugins = $.map(navigator.plugins, function (p) {
            return [
                p.name,
                p.description,
                $.map(p, function (mt) {
                    return [mt.type, mt.suffixes].join("~");
                }).sort().join(",")
            ].join("::");
        }).sort().join(";");
        var rawFP = [
            navigator.userAgent,
            navigator.language, !!window.sessionStorage, false, !!window.indexedDB,
            navigator.doNotTrack,
            fp_screen,
            fp_tzoffset,
            fp_plugins
        ].join("###");
        return $.sha256(rawFP);
    };

    // trans: [size, offx, offy] == [size, 0, offx * size, 0, size, offy * size] or [a11 a12 a13 a21 a22 a23]
    $.ctxDrawPolygon = function (ctx, color, arr, trans) {
        if (!ctx) {
            return;
        }
        trans = trans || [1, 0, 0, 0, 1, 0];
        arr = $.ctxTransform(arr, trans);
        ctx.beginPath();
        ctx.fillStyle = color;
        ctx.moveTo(arr[0][0], arr[1][0]);
        for (var i = 1; i < arr[0].length; i++) {
            ctx.lineTo(arr[0][i], arr[1][i]);
        }
        ctx.closePath();
        ctx.fill();
        ctx.stroke();
    }

    $.ctxRotate = function (arr, theta) {
        return $.ctxTransform(arr, [Math.cos(theta), -Math.sin(theta), 0, Math.sin(theta), Math.cos(theta), 0]);
    }

    $.ctxTransform = function (arr) {
        var ret;
        for (var i = 1; i < arguments.length; i++) {
            var trans = arguments[i];
            if (trans.length == 3) {
                trans = [trans[0], 0, trans[1] * trans[0], 0, trans[0], trans[2] * trans[0]];
            }
            ret = [
                [],
                []
            ];
            for (var i = 0; i < arr[0].length; i++) {
                ret[0][i] = arr[0][i] * trans[0] + arr[1][i] * trans[1] + trans[2];
                ret[1][i] = arr[0][i] * trans[3] + arr[1][i] * trans[4] + trans[5];
            }
        }
        return ret;
    }

});

/** @define {boolean} */
var DEBUGM = true;
/** @define {boolean} */
var DEBUGWK = false;
var DEBUG = isInWorker ? DEBUGWK : (DEBUGM && !!$.urlParam('debug'));

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (item) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] == item) {
                return i;
            }
        }
        return -1;
    };
}

if (!Function.prototype.bind) {
    Function.prototype.bind = function (oThis) {
        if (typeof this !== 'function') {
            throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
        }
        var aArgs = Array.prototype.slice.call(arguments, 1),
                fToBind = this,
                fNOP = function () {},
                fBound = function () {
                    return fToBind.apply(this instanceof fNOP ?
                            this :
                            oThis,
                            aArgs.concat(Array.prototype.slice.call(arguments)));
                };
        if (this.prototype) {
            fNOP.prototype = this.prototype;
        }
        fBound.prototype = new fNOP();
        return fBound;
    };
}


/***************************************** tools.js *********************************************/

var tools = execMain(function () {
    var curScramble = ['-', '', 0];
    var isEn = false;

    var divs = [];

    function execFunc(idx, signal) {
        if (idx == -1) {
            for (var i = 0; i < kernel.getProp('NTools'); i++) {
                execFunc(i, signal);
            }
            return;
        }
        if (!isEn) {
            for (var tool in toolBox) {
                toolBox[tool]();
            }
            fdivs[idx].empty();
            return;
        }

        for (var tool in toolBox) {
            if (tool == funcs[idx]) {
                toolBox[tool](fdivs[idx], signal);
            }
        }
    }

    function disableFunc(idx, signal) {
        for (var tool in toolBox) {
            if (tool == funcs[idx]) {
                toolBox[tool](undefined, signal);
            }
        }
    }

    function scrambleType(scramble) {
        if (scramble.match(/^([\d]?[xyzFRUBLDfrubldSME]([w]|&sup[\d];)?[2']?\s*)+$/) == null) {
            return '-';
        } else if (scramble.match(/^([xyzFRU][2']?\s*)+$/)) {
            return '222o';
        } else if (scramble.match(/^([xyzFRUBLDSME][2']?\s*)+$/)) {
            return '333';
        } else if (scramble.match(/^(([xyzFRUBLDfru]|[FRU]w)[2']?\s*)+$/)) {
            return '444';
        } else if (scramble.match(/^(([xyzFRUBLDfrubld])[w]?[2']?\s*)+$/)) {
            return '555';
        } else {
            return '-';
        }
    }

    function isPuzzle(puzzle, scramble) {
        scramble = scramble || curScramble;
        var scrPuzzle = puzzleType(scramble[0]);
        scramble = scramble[1];
        if (scrPuzzle) {
            return scrPuzzle == puzzle;
        } else if (puzzle == '222') {
            return scramble.match(/^([xyzFRU][2']?\s*)+$/);
        } else if (puzzle == '333') {
            return scramble.match(/^([xyzFRUBLDSME][2']?\s*)+$/);
        } else if (puzzle == '444') {
            return scramble.match(/^(([xyzFRUBLDfru]|[FRU]w)[2']?\s*)+$/);
        } else if (puzzle == '555') {
            return scramble.match(/^(([xyzFRUBLDfrubld])[w]?[2']?\s*)+$/);
        } else if (puzzle == 'skb') {
            return scramble.match(/^([RLUB]'?\s*)+$/);
        } else if (puzzle == 'pyr') {
            return scramble.match(/^([RLUBrlub]'?\s*)+$/);
        } else if (puzzle == 'sq1') {
            return scramble.match(/^$/);
        }
        return false;
    }

    function puzzleType(scrambleType) { 
        if (scrambleType === '222') {
            return "222";
        } else if (/^(333|333oh|333ft|333fm|333bf|333mbf)$/.exec(scrambleType)) {
            return "333";
        } else if (/^(444|444bf)$/.exec(scrambleType)) {
            return "444";
        } else if (/^(555|555bf)$/.exec(scrambleType)) {
            return "555";
        } else if (/^(666(si|[sp]|wca)?|6edge)$/.exec(scrambleType)) {
            return "666";
        } else if (/^(777(si|[sp]|wca)?|7edge)$/.exec(scrambleType)) {
            return "777";
        } else if (/^888$/.exec(scrambleType)) {
            return "888";
        } else if (/^999$/.exec(scrambleType)) {
            return "999";
        } else if (/^101010$/.exec(scrambleType)) {
            return "101010";
        } else if (/^111111$/.exec(scrambleType)) {
            return "111111";
        } else if (/^cubennn$/.exec(scrambleType)) {
            return "cubennn";
        } else if (scrambleType === 'pyram') {
            return "pyr";
        } else if (scrambleType === 'skewb') {
            return "skb";
        } else if (scrambleType === 'sq1') {
            return "sq1";
        } else if (/^clk(wca|o)|clock$/.exec(scrambleType)) {
            return "clk";
        } else if (scrambleType === 'minx') {
            return "mgm";
        } else if (/^15p(at|ra?p?)?$/.exec(scrambleType)) {
            return "15p";
        } else if (/^15p(rmp|m)$/.exec(scrambleType)) {
            return "15b";
        } else if (/^8p(at|ra?p?)?$/.exec(scrambleType)) {
            return "8p";
        } else if (/^8p(rmp|m)$/.exec(scrambleType)) {
            return "8b";
        }
    }

    var fdivs = [];
    var funcs = ['image', 'stats', 'cross'];
    var funcSpan = [];
    var funcMenu = [];
    var funcData = [];

    // for (var i = 0; i < 4; i++) {
    // 	fdivs[i] = $('<div />');
    // 	funcSpan[i] = $('<span />');
    // 	funcMenu[i] = new kernel.TwoLvMenu(funcData, onFuncSelected.bind(null, i), $('<select />'), $('<select />'));
    // 	divs[i] = $('<div />').css('display', 'inline-block');
    // }

    function onFuncSelected(idx, val) {
        DEBUG && console.log('[func select]', idx, val);
        kernel.blur();
        var start = idx === undefined ? 0 : idx;
        var end = idx === undefined ? 4 : idx + 1;
        for (var i = start; i < end; i++) {
            var newVal = funcMenu[i].getSelected();
            if (funcs[i] != newVal) {
                disableFunc(i, 'property');
                funcs[i] = newVal;
                kernel.setProp('toolsfunc', JSON.stringify(funcs));
                execFunc(i, 'property');
            }
        }
    }

    function procSignal(signal, value) {
        if (signal == 'property') {
            if (value[0] == 'imgSize' || /^col/.exec(value[0])) {
                for (var i = 0; i < kernel.getProp('NTools'); i++) {
                    if (funcs[i] == 'image') {
                        execFunc(i, signal);
                    }
                }
            } else if (value[0] == 'NTools') {
                for (var i = 0; i < 4; i++) {
                    if (i < value[1]) {
                        divs[i].show();
                        if (fdivs[i].html() == '') {
                            execFunc(i, signal);
                        }
                    } else {
                        divs[i].hide();
                        disableFunc(i, signal);
                    }
                }
            } else if (value[0] == 'toolHide') {
                toggleFuncSpan(!value[1]);
            } else if (value[0] == 'toolsfunc' && value[2] == 'session') {
                var newfuncs = JSON.parse(value[1]);
                for (var i = 0; i < 4; i++) {
                    funcMenu[i].loadVal(newfuncs[i]);
                }
                onFuncSelected();
            }
        } else if (signal == 'scramble' || signal == 'scrambleX') {
            curScramble = value;
            execFunc(-1, signal);
        } else if (signal == 'button' && value[0] == 'tools') {
            isEn = value[1];
            if (!isEn) {
                execFunc(-1, signal);
                return;
            }
            for (var i = 0; i < kernel.getProp('NTools'); i++) {
                if (isEn && fdivs[i].html() == '') {
                    execFunc(i, signal);
                }
            }
        }
    }

    function toggleFuncSpan(isShow) {
        for (var i = 0; i < 4; i++) {
            if (isShow) {
                funcSpan[i].show();
            } else {
                funcSpan[i].hide();
            }
        }
    }

    function showFuncSpan(e) {
        if ($(e.target).hasClass('click') || $(e.target).is('input, textarea, select')) {
            return;
        }
        kernel.setProp('toolHide', false);
    }

    // $(function() {
    // 	kernel.regListener('tools', 'property', procSignal, /^(?:imgSize|image|toolsfunc|NTools|col(?:cube|pyr|skb|sq1|mgm)|toolHide)$/);
    // 	kernel.regListener('tools', 'scramble', procSignal);
    // 	kernel.regListener('tools', 'scrambleX', procSignal);
    // 	kernel.regListener('tools', 'button', procSignal, /^tools$/);

    // 	var mainDiv = $('<div id="toolsDiv"/>');
    // 	for (var i = 0; i < 4; i++) {
    // 		fdivs[i].click(showFuncSpan);
    // 		funcSpan[i].append("<br>", TOOLS_SELECTFUNC, funcMenu[i].select1, funcMenu[i].select2);
    // 		divs[i].append(fdivs[i], funcSpan[i]).appendTo(mainDiv);
    // 		if (i == 1) {
    // 			mainDiv.append('<br>');
    // 		}
    // 	}

    // 	kernel.regProp('tools', 'solSpl', 0, PROPERTY_HIDEFULLSOL, [false]);
    // 	kernel.regProp('tools', 'imgSize', 2, PROPERTY_IMGSIZE, [15, 5, 50]);
    // 	kernel.regProp('tools', 'NTools', 2, PROPERTY_NTOOLS, [1, 1, 4]);
    // 	var defaultFunc = JSON.stringify(['image', 'stats', 'cross', 'distribution']);
    // 	kernel.regProp('tools', 'toolsfunc', 5, PROPERTY_TOOLSFUNC, [defaultFunc], 1);
    // 	var funcStr = kernel.getProp('toolsfunc', defaultFunc);
    // 	if (funcStr.indexOf('[') == -1) {
    // 		funcStr = defaultFunc.replace('image', funcStr);
    // 		kernel.setProp('toolsfunc', funcStr);
    // 	}
    // 	funcs = JSON.parse(funcStr);
    // 	kernel.addWindow('tools', BUTTON_TOOLS, mainDiv, false, true, 6);
    // 	kernel.regProp('ui', 'toolHide', ~0, 'Hide Tools Selector', [false]);
    // });

    /**
     *	{name: function(fdiv, updateAll) , }
     */
    var toolBox = {};

    function regTool(name, str, execFunc) {
        // DEBUG && console.log('[regtool]', name, str);
        // toolBox[name] = execFunc;
        // str = str.split('>');
        // if (str.length == 2) {
        // 	var idx1 = -1;
        // 	for (var i = 0; i < funcData.length; i++) {
        // 		if (funcData[i][0] == str[0] && $.isArray(funcData[i][1])) {
        // 			idx1 = i;
        // 			break;
        // 		}
        // 	}
        // 	if (idx1 != -1) {
        // 		funcData[idx1][1].push([str[1], name]);
        // 	} else {
        // 		funcData.push([str[0], [[str[1], name]]]);
        // 	}
        // } else {
        // 	funcData.push([str[0], name]);
        // }
        // for (var i = 0; i < 4; i++) {
        // 	funcMenu[i].reset(funcs[i]);
        // }
    }

    function getSolutionSpan(solution) {
        var span = $('<span />');
        for (var i = 0; i < solution.length; i++) {
            span.append('<span style="display:none;">&nbsp;' + solution[i] + '</span>');
        }
        if (kernel.getProp('solSpl')) {
            span.append($('<span class="click" data="n">[+1]</span>').click(procSolutionClick));
            span.append($('<span class="click" data="a">[' + solution.length + 'f]</span>').click(procSolutionClick));
        } else {
            span.children().show();
        }
        return span;
    }

    function procSolutionClick(e) {
        var span = $(this);
        if (span.attr('data') == 'a') {
            span.prevAll().show();
            span.prev().hide();
            span.hide();
        } else if (span.attr('data') == 'n') {
            var unshown = span.prevAll(':hidden');
            unshown.last().show();
            if (unshown.length == 1) {
                span.next().hide();
                span.hide();
            }
        }
    }

    return {
        regTool: regTool,
        getCurScramble: function () {
            return curScramble;
        },
        getSolutionSpan: getSolutionSpan,
        scrambleType: scrambleType,
        puzzleType: puzzleType,
        isPuzzle: isPuzzle
    };
});


/***************************************** mersennetwister.js *********************************************/
//
//  Version     File name           Description
//  -------     ---------           -----------
//  2004-12-03  hr$mersennetwister.js       original version will stay available,
//                          but is no longer maintained by Henk Reints
//
//  2005-11-02  hr$mersennetwister2.js      o  renamed constructor from "MersenneTwister"
//                             to "MersenneTwisterObject"
//                          o  exposure of methods now in separate section near the end
//                          o  removed "this." from internal references
//
// ====================================================================================================================
// Mersenne Twister mt19937ar, a pseudorandom generator by Takuji Nishimura and Makoto Matsumoto.
// Object Oriented JavaScript version by Henk Reints (http://henk-reints.nl)
// ====================================================================================================================
// Original header text from the authors (reformatted a little bit by HR):
// -----------------------------------------------------------------------
//
//  A C-program for MT19937, with initialization improved 2002/1/26.
//  Coded by Takuji Nishimura and Makoto Matsumoto.
//
//  Before using, initialize the state by using init_genrand(seed) or init_by_array(init_key, key_length).
//
//  Copyright (C) 1997 - 2002, Makoto Matsumoto and Takuji Nishimura, All rights reserved.
//
//  Redistribution and use in source and binary forms, with or without modification,
//  are permitted provided that the following conditions are met:
//
//  1. Redistributions of source code must retain the above copyright notice,
//     this list of conditions and the following disclaimer.
//
//  2. Redistributions in binary form must reproduce the above copyright notice,
//     this list of conditions and the following disclaimer in the documentation and/or
//     other materials provided with the distribution.
//
//  3. The names of its contributors may not be used to endorse or promote products derived from this software
//     without specific prior written permission.
//
//  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED
//  WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
//  PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
//  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
//  TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
//  HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
//  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
//  POSSIBILITY OF SUCH DAMAGE.
//
//  Any feedback is very welcome.
//  http://www.math.sci.hiroshima-u.ac.jp/~m-mat/MT/emt.html
//  email: m-mat @ math.sci.hiroshima-u.ac.jp (remove space)
//
// ====================================================================================================================
// Remarks by Henk Reints about this JS version:
//
// Legal stuff:
//  THE ABOVE LEGAL NOTICES AND DISCLAIMER BY THE ORIGINAL AUTHORS
//  ALSO APPLY TO THIS JAVASCRIPT TRANSLATION BY HENK REINTS.
//
// Contact:
//  For feedback or questions you can find me on the internet: http://henk-reints.nl
//
// Description:
//  This is an Object Oriented JavaScript version of the Mersenne Twister.
//
// Constructor:
//  MersenneTwisterObject([seed[,seedArray]])
//      if called with 0 args then a default seed   is used for initialisation by the 'init' method;
//      if called with 1 arg  then 'seed'           is used for initialisation by the 'init' method;
//      if called with 2 args then 'seedArray,seed' is used for initialisation by the 'initByArray' method;
//      if a supplied seed is NaN or not given then a default is used.
//
// Properties:
//  none exposed
//
// Methods:
//  init0(seed)     initialises the state array using the original algorithm
//                if seed is NaN or not given then a default is used
//  init(seed)      initialises the state array using the improved algorithm
//                if seed is NaN or not given then a default is used
//  initByArray(seedArray[,seed])
//              initialises the state array based on an array of seeds,
//                the 2nd argument is optional, if given and not NaN then it overrides
//                the default seed which is used for the very first initialisation
//  skip(n)         lets the random number generator skip a given count of randoms
//                if n <= 0 then it advances to the next scrambling round
//                in order to produce an unpredictable well-distributed sequence, you could let n be
//                generated by some other random generator which preferrably uses external events to
//                create an entropy pool from which to take the numbers.
//                this method has been added by Henk Reints, 2004-11-16.
//  randomInt32()       returns a random 32-bit integer
//  randomInt53()       returns a random 53-bit integer
//                this is done in the same way as was introduced 2002/01/09 by Isaku Wada
//                in his genrand_res53() function
//  randomReal32()      returns a random floating point number in [0,1) with 32-bit precision
//                please note that - at least on Microsoft Platforms - JavaScript ALWAYS stores
//                Numbers with a 53 bit mantissa, so randomReal32() is not the best choice in JS.
//                it is provided to be able to produce output that can be compared to the demo
//                output given by the original authors. For JavaScript implementations I suggest
//                you always use the randomReal53 method.
//  randomReal53()      returns a random floating point number in [0,1) with 53-bit precision
//                this is done in the same way as was introduced 2002/01/09 by Isaku Wada
//                in the genrand_res53() function
//  randomString(len)   returns a random string of given length, existing of chars from the charset:
//                "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/", which is identical
//                to the character set used for base64 encoding, so effectively it generates a random
//                base64-encoded number of arbitrary precision.
//                If you intend to use a random string in a URL string, then the "+" and "/" should
//                be converted to URL syntax using the JavaScript built-in 'escape' method.
//                this method has been added by Henk Reints, 2004-11-16.
//  random()        a synonym for randomReal53  [HR/2004-12-03]
//  randomInt()     a synonym for randomInt32   [HR/2004-12-03]
//                these two synonyms are intended to be generic names for normal use.
//
// Examples of object creation:
//  mt = new MersenneTwisterObject()            // create object with default initialisation
//  mt = new MersenneTwisterObject(19571118)        // create object using a specific seed
//  mt = new MersenneTwisterObject(Nan,[1957,11,18,03,06])  // create object using a seed array only
//  mt = new MersenneTwisterObject(1957,[11,18,03,06])  // create object using a seed array AND a specific seed
//
// Examples of (re)initialisation (to be done after the object has been created):
//  mt.init0()              // re-init using the old-style algorithm with its default seed
//  mt.init0(181157)            // re-init using the old-style algorithm with a given seed
//  mt.init()               // re-init using the new-style algorithm with its default seed
//  mt.init(181157)             // re-init using the new-style algorithm with a given seed
//  mt.initByArray([18,11,57])      // re-init using a seed array
//  mt.initByArray([18,11,57],0306)     // re-init using a seed array AND a specific seed
//
// Example of generating random numbers (after creation of the object and optional re-initialisation of its state):
//  while (condition)
//  {   i = mt.randomInt32()            // get a random 32 bit integer
//      a = mt.randomReal53()           // and a random floating point number of maximum precision
//      x = myVerySophisticatedAlgorithm(i,a)   // do something with it
//  }
//
// Functions for internal use only:
//  dmul0(m,n)  performs double precision multiplication of two 32-bit integers and returns only the low order
//          32 bits of the product; this function is necessary because JS always stores Numbers with a
//          53-bit mantissa, leading to loss of 11 lowest order bits. In fact it is the pencil & paper
//          method for multiplying 2 numbers of 2 digits each, but it uses digits of 16-bits each. Since
//          only the low order result is needed, the steps that only affect the high order part of the
//          result are left out.
//
// Renamed original functions:          to:
//  init_genrand(s)             init(seed)
//  init_by_array(init_key,key_length)  initByArray(seedArray[,seed])
//  genrand_int32()             randomInt32()
//  genrand_real2()             randomReal32()
//  genrand_res53()             randomReal53()
//
// Other modifications w.r.t. the original:
//  - did not include the other variants returning real values - I think [0,1) is the only appropriate interval;
//  - included randomInt53() using the same method as was introduced 2002/01/09 by Isaku Wada in his genrand_res53;
//  - included randomString(len);
//  - included skip(n);
//  - in the randomInt32 method I have changed the check "if (mti >= N)" to a 'while' loop decrementing mti by N
//    in each iteration, which allows skipping a range of randoms by simply adding a value to the mti property.
//    By setting mti to a negative value you can force an advance to the next scrambling round.
//    Since in this library the uninitialised state is not marked by mti==N+1 that's is a safe algorithm.
//    When using the constructor, a default initialisation is always performed.
//
// Notes:
//  - Whenever I say 'random' in this file, I mean of course 'pseudorandom';
//  - I have tested this only with Windows Script Host V5.6 on 32-bit Microsoft Windows platforms.
//    If it does not produce correct results on other platforms, then please don't blame me!
//  - As mentioned by the authors and on many other internet sites,
//    the Mersenne Twister does _NOT_ produce secure sequences for cryptographic purposes!
//    It was primarily designed for producing good pseudorandom numbers to perform statistics.
// ====================================================================================================================

function MersenneTwisterObject(seed, seedArray) {
    var N = 624,
            mask = 0xffffffff,
            mt = [],
            mti = NaN,
            m01 = [0, 0x9908b0df]
    var M = 397,
            N1 = N - 1,
            NM = N - M,
            MN = M - N,
            U = 0x80000000,
            L = 0x7fffffff,
            R = 0x100000000

    function dmul0(m, n) {
        var H = 0xffff0000,
                L = 0x0000ffff,
                R = 0x100000000,
                m0 = m & L,
                m1 = (m & H) >>> 16,
                n0 = n & L,
                n1 = (n & H) >>> 16,
                p0, p1, x
        p0 = m0 * n0, p1 = p0 >>> 16, p0 &= L, p1 += m0 * n1, p1 &= L, p1 += m1 * n0, p1 &= L, x = (p1 << 16) | p0
        return (x < 0 ? x + R : x)
    }

    function init0(seed) {
        var x = (arguments.length > 0 && isFinite(seed) ? seed & mask : 4357),
                i
        for (mt = [x], mti = N, i = 1; i < N; mt[i++] = x = (69069 * x) & mask) {
        }
    }

    function init(seed) {
        var x = (arguments.length > 0 && isFinite(seed) ? seed & mask : 5489),
                i
        for (mt = [x], mti = N, i = 1; i < N; mt[i] = x = dmul0(x ^ (x >>> 30), 1812433253) + i++) {
        }
    }

    function initByArray(seedArray, seed) {
        var N1 = N - 1,
                L = seedArray.length,
                x, i, j, k
        init(arguments.length > 1 && isFinite(seed) ? seed : 19650218)
        x = mt[0], i = 1, j = 0, k = Math.max(N, L)
        for (; k; j %= L, k--) {
            mt[i] = x = ((mt[i++] ^ dmul0(x ^ (x >>> 30), 1664525)) + seedArray[j] + j++) & mask
            if (i > N1) {
                mt[0] = x = mt[N1];
                i = 1
            }
        }
        for (k = N - 1; k; k--) {
            mt[i] = x = ((mt[i] ^ dmul0(x ^ (x >>> 30), 1566083941)) - i++) & mask
            if (i > N1) {
                mt[0] = x = mt[N1];
                i = 1
            }
        }
        mt[0] = 0x80000000
    }

    function skip(n) {
        mti = (n <= 0 ? -1 : mti + n)
    }

    function randomInt32() {
        var y, k
        while (mti >= N || mti < 0) {
            mti = Math.max(0, mti - N)
            for (k = 0; k < NM; y = (mt[k] & U) | (mt[k + 1] & L), mt[k] = mt[k + M] ^ (y >>> 1) ^ m01[y & 1], k++) {
            }
            for (; k < N1; y = (mt[k] & U) | (mt[k + 1] & L), mt[k] = mt[k + MN] ^ (y >>> 1) ^ m01[y & 1], k++) {
            }
            y = (mt[N1] & U) | (mt[0] & L), mt[N1] = mt[M - 1] ^ (y >>> 1) ^ m01[y & 1]
        }
        y = mt[mti++], y ^= (y >>> 11), y ^= (y << 7) & 0x9d2c5680, y ^= (y << 15) & 0xefc60000, y ^= (y >>> 18)
        return (y < 0 ? y + R : y)
    }

    function randomInt53() {
        var two26 = 0x4000000
        return (randomInt32() >>> 5) * two26 + (randomInt32() >>> 6)
    }

    function randomReal32() {
        var two32 = 0x100000000
        return randomInt32() / two32
    }

    function randomReal53() {
        var two53 = 0x20000000000000
        return randomInt53() / two53
    }

    function randomString(len) {
        var i, r, x = "",
                C = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"
        for (i = 0; i < len; x += C.charAt((((i++) % 5) > 0 ? r : r = randomInt32()) & 63), r >>>= 6) {
        }
        ;
        return x
    }
    if (arguments.length > 1)
        initByArray(seedArray, seed)
    else if (arguments.length > 0)
        init(seed)
    else
        init()
    return randomReal53;
}
// ====================================================================================================================
// End of file hr$mersennetwister2.js - Copyright (c) 2004,2005 Henk Reints, http://henk-reints.nl

Math.random = new MersenneTwisterObject(new Date().getTime());

/***************************************** mathlib.js *********************************************/

var mathlib = (function () {
    var Cnk = [],
            fact = [1];
    for (var i = 0; i < 32; ++i) {
        Cnk[i] = [];
        for (var j = 0; j < 32; ++j) {
            Cnk[i][j] = 0;
        }
    }
    for (var i = 0; i < 32; ++i) {
        Cnk[i][0] = Cnk[i][i] = 1;
        fact[i + 1] = fact[i] * (i + 1);
        for (var j = 1; j < i; ++j) {
            Cnk[i][j] = Cnk[i - 1][j - 1] + Cnk[i - 1][j];
        }
    }

    function circleOri(arr, a, b, c, d, ori) {
        var temp = arr[a];
        arr[a] = arr[d] ^ ori;
        arr[d] = arr[c] ^ ori;
        arr[c] = arr[b] ^ ori;
        arr[b] = temp ^ ori;
    }

    function circle(arr) {
        var length = arguments.length - 1,
                temp = arr[arguments[length]];
        for (var i = length; i > 1; i--) {
            arr[arguments[i]] = arr[arguments[i - 1]];
        }
        arr[arguments[1]] = temp;
        return circle;
    }

    //perm: [idx1, idx2, ..., idxn]
    //pow: 1, 2, 3, ...
    //ori: ori1, ori2, ..., orin, base
    // arr[perm[idx2]] = arr[perm[idx1]] + ori[idx2] - ori[idx1] + base
    function acycle(arr, perm, pow, ori) {
        pow = pow || 1;
        var plen = perm.length;
        var tmp = [];
        for (var i = 0; i < plen; i++) {
            tmp[i] = arr[perm[i]];
        }
        for (var i = 0; i < plen; i++) {
            var j = (i + pow) % plen;
            arr[perm[j]] = tmp[i];
            if (ori) {
                arr[perm[j]] += ori[j] - ori[i] + ori[ori.length - 1];
            }
        }
        return acycle;
    }

    function getPruning(table, index) {
        return table[index >> 3] >> ((index & 7) << 2) & 15;
    }

    function setNPerm(arr, idx, n) {
        var i, j;
        arr[n - 1] = 0;
        for (i = n - 2; i >= 0; --i) {
            arr[i] = idx % (n - i);
            idx = ~~(idx / (n - i));
            for (j = i + 1; j < n; ++j) {
                arr[j] >= arr[i] && ++arr[j];
            }
        }
    }

    function getNPerm(arr, n) {
        var i, idx, j;
        idx = 0;
        for (i = 0; i < n; ++i) {
            idx *= n - i;
            for (j = i + 1; j < n; ++j) {
                arr[j] < arr[i] && ++idx;
            }
        }
        return idx;
    }

    function getNParity(idx, n) {
        var i, p;
        p = 0;
        for (i = n - 2; i >= 0; --i) {
            p ^= idx % (n - i);
            idx = ~~(idx / (n - i));
        }
        return p & 1;
    }

    function get8Perm(arr, n, even) {
        n = n || 8;
        var idx = 0;
        var val = 0x76543210;
        for (var i = 0; i < n - 1; ++i) {
            var v = arr[i] << 2;
            idx = (n - i) * idx + (val >> v & 7);
            val -= 0x11111110 << v;
        }
        return even < 0 ? (idx >> 1) : idx;
    }

    function set8Perm(arr, idx, n, even) {
        n = (n || 8) - 1;
        var val = 0x76543210;
        var prt = 0;
        if (even < 0) {
            idx <<= 1;
        }
        for (var i = 0; i < n; ++i) {
            var p = fact[n - i];
            var v = ~~(idx / p);
            prt ^= v;
            idx %= p;
            v <<= 2;
            arr[i] = val >> v & 7;
            var m = (1 << v) - 1;
            val = (val & m) + (val >> 4 & ~m);
        }
        if (even < 0 && (prt & 1) != 0) {
            arr[n] = arr[n - 1];
            arr[n - 1] = val & 7;
        } else {
            arr[n] = val & 7;
        }
        return arr;
    }

    function getNOri(arr, n, evenbase) {
        var base = Math.abs(evenbase);
        var idx = evenbase < 0 ? 0 : arr[0] % base;
        for (var i = n - 1; i > 0; i--) {
            idx = idx * base + arr[i] % base;
        }
        return idx;
    }

    function setNOri(arr, idx, n, evenbase) {
        var base = Math.abs(evenbase);
        var parity = base * n;
        for (var i = 1; i < n; i++) {
            arr[i] = idx % base;
            parity -= arr[i];
            idx = ~~(idx / base);
        }
        arr[0] = (evenbase < 0 ? parity : idx) % base;
        return arr;
    }

    // type: 'p', 'o'
    // evenbase: base for ori, sign for even parity
    function coord(type, length, evenbase) {
        this.length = length;
        this.evenbase = evenbase;
        this.get = type == 'p' ?
                function (arr) {
                    return get8Perm(arr, this.length, this.evenbase);
                } : function (arr) {
            return getNOri(arr, this.length, this.evenbase);
        };
        this.set = type == 'p' ?
                function (arr, idx) {
                    return set8Perm(arr, idx, this.length, this.evenbase);
                } : function (arr, idx) {
            return setNOri(arr, idx, this.length, this.evenbase);
        };
    }

    function fillFacelet(facelets, f, perm, ori, divcol) {
        for (var i = 0; i < facelets.length; i++) {
            for (var j = 0; j < facelets[i].length; j++) {
                f[facelets[i][(j + ori[i]) % facelets[i].length]] = ~~(facelets[perm[i]][j] / divcol);
            }
        }
    }

    function createMove(moveTable, size, doMove, N_MOVES) {
        N_MOVES = N_MOVES || 6;
        if ($.isArray(doMove)) {
            var cord = new coord(doMove[1], doMove[2], doMove[3]);
            doMove = doMove[0];
            for (var j = 0; j < N_MOVES; j++) {
                moveTable[j] = [];
                for (var i = 0; i < size; i++) {
                    var arr = cord.set([], i);
                    doMove(arr, j);
                    moveTable[j][i] = cord.get(arr);
                }
            }
        } else {
            for (var j = 0; j < N_MOVES; j++) {
                moveTable[j] = [];
                for (var i = 0; i < size; i++) {
                    moveTable[j][i] = doMove(i, j);
                }
            }
        }
    }

    function edgeMove(arr, m) {
        if (m == 0) { //F
            circleOri(arr, 0, 7, 8, 4, 1);
        } else if (m == 1) { //R
            circleOri(arr, 3, 6, 11, 7, 0);
        } else if (m == 2) { //U
            circleOri(arr, 0, 1, 2, 3, 0);
        } else if (m == 3) { //B
            circleOri(arr, 2, 5, 10, 6, 1);
        } else if (m == 4) { //L
            circleOri(arr, 1, 4, 9, 5, 0);
        } else if (m == 5) { //D
            circleOri(arr, 11, 10, 9, 8, 0);
        }
    }

    function CubieCube() {
        this.ca = [0, 1, 2, 3, 4, 5, 6, 7];
        this.ea = [0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22];
    }

    CubieCube.EdgeMult = function (a, b, prod) {
        for (var ed = 0; ed < 12; ed++) {
            prod.ea[ed] = a.ea[b.ea[ed] >> 1] ^ (b.ea[ed] & 1);
        }
    };

    CubieCube.CornMult = function (a, b, prod) {
        for (var corn = 0; corn < 8; corn++) {
            var ori = ((a.ca[b.ca[corn] & 7] >> 3) + (b.ca[corn] >> 3)) % 3;
            prod.ca[corn] = a.ca[b.ca[corn] & 7] & 7 | ori << 3;
        }
    };

    CubieCube.CubeMult = function (a, b, prod) {
        CubieCube.CornMult(a, b, prod);
        CubieCube.EdgeMult(a, b, prod);
    };

    CubieCube.prototype.init = function (ca, ea) {
        this.ca = ca.slice();
        this.ea = ea.slice();
        return this;
    };

    CubieCube.prototype.isEqual = function (c) {
        for (var i = 0; i < 8; i++) {
            if (this.ca[i] != c.ca[i]) {
                return false;
            }
        }
        for (var i = 0; i < 12; i++) {
            if (this.ea[i] != c.ea[i]) {
                return false;
            }
        }
        return true;
    };

    var cornerFacelet = [
        [8, 9, 20],
        [6, 18, 38],
        [0, 36, 47],
        [2, 45, 11],
        [29, 26, 15],
        [27, 44, 24],
        [33, 53, 42],
        [35, 17, 51]
    ];
    var edgeFacelet = [
        [5, 10],
        [7, 19],
        [3, 37],
        [1, 46],
        [32, 16],
        [28, 25],
        [30, 43],
        [34, 52],
        [23, 12],
        [21, 41],
        [50, 39],
        [48, 14]
    ];

    CubieCube.prototype.toFaceCube = function (cFacelet, eFacelet) {
        cFacelet = cFacelet || cornerFacelet;
        eFacelet = eFacelet || edgeFacelet;
        var ts = "URFDLB";
        var f = [];
        for (var i = 0; i < 54; i++) {
            f[i] = ts[~~(i / 9)];
        }
        for (var c = 0; c < 8; c++) {
            var j = this.ca[c] & 0x7; // cornercubie with index j is at
            var ori = this.ca[c] >> 3; // Orientation of this cubie
            for (var n = 0; n < 3; n++)
                f[cFacelet[c][(n + ori) % 3]] = ts[~~(cFacelet[j][n] / 9)];
        }
        for (var e = 0; e < 12; e++) {
            var j = this.ea[e] >> 1; // edgecubie with index j is at edgeposition
            var ori = this.ea[e] & 1; // Orientation of this cubie
            for (var n = 0; n < 2; n++)
                f[eFacelet[e][(n + ori) % 2]] = ts[~~(eFacelet[j][n] / 9)];
        }
        return f.join("");
    }

    CubieCube.prototype.invFrom = function (cc) {
        for (var edge = 0; edge < 12; edge++) {
            this.ea[cc.ea[edge] >> 1] = edge << 1 | cc.ea[edge] & 1;
        }
        for (var corn = 0; corn < 8; corn++) {
            this.ca[cc.ca[corn] & 0x7] = corn | 0x20 >> (cc.ca[corn] >> 3) & 0x18;
        }
        return this;
    }

    CubieCube.prototype.fromFacelet = function (facelet, cFacelet, eFacelet) {
        cFacelet = cFacelet || cornerFacelet;
        eFacelet = eFacelet || edgeFacelet;
        var count = 0;
        var f = [];
        var centers = facelet[4] + facelet[13] + facelet[22] + facelet[31] + facelet[40] + facelet[49];
        for (var i = 0; i < 54; ++i) {
            f[i] = centers.indexOf(facelet[i]);
            if (f[i] == -1) {
                return -1;
            }
            count += 1 << (f[i] << 2);
        }
        if (count != 0x999999) {
            return -1;
        }
        var col1, col2, i, j, ori;
        for (i = 0; i < 8; ++i) {
            for (ori = 0; ori < 3; ++ori)
                if (f[cFacelet[i][ori]] == 0 || f[cFacelet[i][ori]] == 3)
                    break;
            col1 = f[cFacelet[i][(ori + 1) % 3]];
            col2 = f[cFacelet[i][(ori + 2) % 3]];
            for (j = 0; j < 8; ++j) {
                if (col1 == ~~(cFacelet[j][1] / 9) && col2 == ~~(cFacelet[j][2] / 9)) {
                    this.ca[i] = j | ori % 3 << 3;
                    break;
                }
            }
        }
        for (i = 0; i < 12; ++i) {
            for (j = 0; j < 12; ++j) {
                if (f[eFacelet[i][0]] == ~~(eFacelet[j][0] / 9) && f[eFacelet[i][1]] == ~~(eFacelet[j][1] / 9)) {
                    this.ea[i] = j << 1;
                    break;
                }
                if (f[eFacelet[i][0]] == ~~(eFacelet[j][1] / 9) && f[eFacelet[i][1]] == ~~(eFacelet[j][0] / 9)) {
                    this.ea[i] = j << 1 | 1;
                    break;
                }
            }
        }
        return this;
    }

    var moveCube = [];
    for (var i = 0; i < 18; i++) {
        moveCube[i] = new CubieCube();
    }
    moveCube[0].init([3, 0, 1, 2, 4, 5, 6, 7], [6, 0, 2, 4, 8, 10, 12, 14, 16, 18, 20, 22]);
    moveCube[3].init([20, 1, 2, 8, 15, 5, 6, 19], [16, 2, 4, 6, 22, 10, 12, 14, 8, 18, 20, 0]);
    moveCube[6].init([9, 21, 2, 3, 16, 12, 6, 7], [0, 19, 4, 6, 8, 17, 12, 14, 3, 11, 20, 22]);
    moveCube[9].init([0, 1, 2, 3, 5, 6, 7, 4], [0, 2, 4, 6, 10, 12, 14, 8, 16, 18, 20, 22]);
    moveCube[12].init([0, 10, 22, 3, 4, 17, 13, 7], [0, 2, 20, 6, 8, 10, 18, 14, 16, 4, 12, 22]);
    moveCube[15].init([0, 1, 11, 23, 4, 5, 18, 14], [0, 2, 4, 23, 8, 10, 12, 21, 16, 18, 7, 15]);
    for (var a = 0; a < 18; a += 3) {
        for (var p = 0; p < 2; p++) {
            CubieCube.EdgeMult(moveCube[a + p], moveCube[a], moveCube[a + p + 1]);
            CubieCube.CornMult(moveCube[a + p], moveCube[a], moveCube[a + p + 1]);
        }
    }

    CubieCube.moveCube = moveCube;

    CubieCube.prototype.edgeCycles = function () {
        var visited = [];
        var small_cycles = [0, 0, 0];
        var cycles = 0;
        var parity = false;
        for (var x = 0; x < 12; ++x) {
            if (visited[x]) {
                continue
            }
            var length = -1;
            var flip = false;
            var y = x;
            do {
                visited[y] = true;
                ++length;
                flip ^= this.ea[y] & 1;
                y = this.ea[y] >> 1;
            } while (y != x);
            cycles += length >> 1;
            if (length & 1) {
                parity = !parity;
                ++cycles;
            }
            if (flip) {
                if (length == 0) {
                    ++small_cycles[0];
                } else if (length & 1) {
                    small_cycles[2] ^= 1;
                } else {
                    ++small_cycles[1];
                }
            }
        }
        small_cycles[1] += small_cycles[2];
        if (small_cycles[0] < small_cycles[1]) {
            cycles += (small_cycles[0] + small_cycles[1]) >> 1;
        } else {
            var flip_cycles = [0, 2, 3, 5, 6, 8, 9];
            cycles += small_cycles[1] + flip_cycles[(small_cycles[0] - small_cycles[1]) >> 1];
        }
        return cycles - parity;
    };

    function createPrun(prun, init, size, maxd, doMove, N_MOVES, N_POWER, N_INV) {
        var isMoveTable = $.isArray(doMove);
        N_MOVES = N_MOVES || 6;
        N_POWER = N_POWER || 3;
        N_INV = N_INV || 256;
        maxd = maxd || 256;
        for (var i = 0, len = (size + 7) >>> 3; i < len; i++) {
            prun[i] = -1;
        }
        prun[init >> 3] ^= 15 << ((init & 7) << 2);
        var val = 0;
        // var t = +new Date;
        for (var l = 0; l <= maxd; l++) {
            var done = 0;
            var inv = l >= N_INV;
            var fill = (l + 1) ^ 15;
            var find = inv ? 0xf : l;
            var check = inv ? l : 0xf;

            out: for (var p = 0; p < size; p++, val >>= 4) {
                if ((p & 7) == 0) {
                    val = prun[p >> 3];
                    if (!inv && val == -1) {
                        p += 7;
                        continue;
                    }
                }
                if ((val & 0xf) != find) {
                    continue;
                }
                for (var m = 0; m < N_MOVES; m++) {
                    var q = p;
                    for (var c = 0; c < N_POWER; c++) {
                        q = isMoveTable ? doMove[m][q] : doMove(q, m);
                        if (getPruning(prun, q) != check) {
                            continue;
                        }
                        ++done;
                        if (inv) {
                            prun[p >> 3] ^= fill << ((p & 7) << 2);
                            continue out;
                        }
                        prun[q >> 3] ^= fill << ((q & 7) << 2);
                    }
                }
            }
            if (done == 0) {
                break;
            }
            DEBUG && console.log('[prun]', done);
        }
    }

    //state_params: [[init, doMove, size, [maxd], [N_INV]], [...]...]
    function Solver(N_MOVES, N_POWER, state_params) {
        this.N_STATES = state_params.length;
        this.N_MOVES = N_MOVES;
        this.N_POWER = N_POWER;
        this.state_params = state_params;
        this.inited = false;
    }

    var _ = Solver.prototype;

    _.search = function (state, minl, MAXL) {
        MAXL = (MAXL || 99) + 1;
        if (!this.inited) {
            this.move = [];
            this.prun = [];
            for (var i = 0; i < this.N_STATES; i++) {
                var state_param = this.state_params[i];
                var init = state_param[0];
                var doMove = state_param[1];
                var size = state_param[2];
                var maxd = state_param[3];
                var N_INV = state_param[4];
                this.move[i] = [];
                this.prun[i] = [];
                createMove(this.move[i], size, doMove, this.N_MOVES);
                createPrun(this.prun[i], init, size, maxd, this.move[i], this.N_MOVES, this.N_POWER, N_INV);
            }
            this.inited = true;
        }
        this.sol = [];
        for (var maxl = minl; maxl < MAXL; maxl++) {
            if (this.idaSearch(state, maxl, -1)) {
                break;
            }
        }
        return maxl == MAXL ? null : this.sol.reverse();
    };

    _.toStr = function (sol, move_map, power_map) {
        var ret = [];
        for (var i = 0; i < sol.length; i++) {
            ret.push(move_map[sol[i][0]] + power_map[sol[i][1]]);
        }
        return ret.join(' ').replace(/ +/g, ' ');
    };

    _.idaSearch = function (state, maxl, lm) {
        var N_STATES = this.N_STATES;
        for (var i = 0; i < N_STATES; i++) {
            if (getPruning(this.prun[i], state[i]) > maxl) {
                return false;
            }
        }
        if (maxl == 0) {
            return true;
        }
        var offset = state[0] + maxl + lm + 1;
        for (var move0 = 0; move0 < this.N_MOVES; move0++) {
            var move = (move0 + offset) % this.N_MOVES;
            if (move == lm) {
                continue;
            }
            var cur_state = state.slice();
            for (var power = 0; power < this.N_POWER; power++) {
                for (var i = 0; i < N_STATES; i++) {
                    cur_state[i] = this.move[i][move][cur_state[i]];
                }
                if (this.idaSearch(cur_state, maxl - 1, move)) {
                    this.sol.push([move, power]);
                    return true;
                }
            }
        }
        return false;
    };

    function identity(state) {
        return state;
    }

    // state: string not null
    // solvedStates: [solvedstate, solvedstate, ...], string not null
    // moveFunc: function(state, move);
    // moves: {move: face0 | axis0}, face0 | axis0 = 4 + 4 bits
    function gSolver(solvedStates, doMove, moves, prunHash) {
        this.solvedStates = solvedStates;
        this.doMove = doMove;
        this.movesList = [];
        for (var move in moves) {
            this.movesList.push([move, moves[move]]);
        }
        this.prunHash = prunHash || identity;
        this.prunTable = {};
        this.toUpdateArr = null;
        this.prunTableSize = 0;
        this.prunDepth = -1;
        this.cost = 0;
    }

    _ = gSolver.prototype;

    _.updatePrun = function (targetDepth) {
        targetDepth = targetDepth === undefined ? this.prunDepth + 1 : targetDepth;
        for (var depth = this.prunDepth + 1; depth <= targetDepth; depth++) {
            var t = +new Date;
            if (depth < 1) {
                this.prevSize = 0;
                for (var i = 0; i < this.solvedStates.length; i++) {
                    var state = this.prunHash(this.solvedStates[i]);
                    if (!(state in this.prunTable)) {
                        this.prunTable[state] = depth;
                        this.prunTableSize++;
                    }
                }
            } else {
                this.updatePrunBFS(depth - 1);
            }
            if (this.cost == 0) {
                return;
            }
            this.prunDepth = depth;
            DEBUG && console.log(depth, this.prunTableSize - this.prevSize, +new Date - t);
            this.prevSize = this.prunTableSize;
        }
    };

    _.updatePrunBFS = function (fromDepth) {
        if (this.toUpdateArr == null) {
            this.toUpdateArr = [];
            for (var state in this.prunTable) {
                if (this.prunTable[state] != fromDepth) {
                    continue;
                }
                this.toUpdateArr.push(state);
            }
        }
        while (this.toUpdateArr.length != 0) {
            var state = this.toUpdateArr.pop();
            for (var moveIdx = 0; moveIdx < this.movesList.length; moveIdx++) {
                var newState = this.doMove(state, this.movesList[moveIdx][0]);
                if (!newState || newState in this.prunTable) {
                    continue;
                }
                this.prunTable[newState] = fromDepth + 1;
                this.prunTableSize++;
            }
            if (this.cost >= 0) {
                if (this.cost == 0) {
                    return;
                }
                this.cost--;
            }
        }
        this.toUpdateArr = null;
    };

    _.search = function (state, minl, MAXL) {
        this.sol = [];
        this.subOpt = false;
        this.state = state;
        this.visited = {};
        this.maxl = minl = minl || 0;
        return this.searchNext(MAXL);
    };

    _.searchNext = function (MAXL, cost) {
        MAXL = (MAXL + 1) || 99;
        this.prevSolStr = this.solArr ? this.solArr.join(',') : null;
        this.solArr = null;
        this.cost = cost || -1;
        for (; this.maxl < MAXL; this.maxl++) {
            this.updatePrun(Math.ceil(this.maxl / 2));
            if (this.cost == 0) {
                return null;
            }
            if (this.idaSearch(this.state, this.maxl, null, 0)) {
                break;
            }
        }
        return this.solArr;
    }

    _.getPruning = function (state) {
        var prun = this.prunTable[this.prunHash(state)];
        return prun === undefined ? this.prunDepth + 1 : prun;
    };

    _.idaSearch = function (state, maxl, lm, depth) {
        if (this.getPruning(state) > maxl) {
            return false;
        }
        if (maxl == 0) {
            if (this.solvedStates.indexOf(state) == -1) {
                return false;
            }
            var solArr = this.getSolArr();
            this.subOpt = true;
            if (solArr.join(',') == this.prevSolStr) {
                return false;
            }
            this.solArr = solArr;
            return true;
        }
        if (!this.subOpt) {
            if (state in this.visited && this.visited[state] < depth) {
                return false;
            }
            this.visited[state] = depth;
        }
        if (this.cost >= 0) {
            if (this.cost == 0) {
                return true;
            }
            this.cost--;
        }
        var lastMove = lm == null ? '' : this.movesList[lm][0];
        var lastAxisFace = lm == null ? -1 : this.movesList[lm][1];
        for (var moveIdx = this.sol[depth] || 0; moveIdx < this.movesList.length; moveIdx++) {
            var moveArgs = this.movesList[moveIdx];
            var axisface = moveArgs[1] ^ lastAxisFace;
            var move = moveArgs[0];
            if (axisface == 0 ||
                    (axisface & 0xf) == 0 && move <= lastMove) {
                continue;
            }
            var newState = this.doMove(state, move);
            if (!newState || newState == state) {
                continue;
            }
            this.sol[depth] = moveIdx;
            if (this.idaSearch(newState, maxl - 1, moveIdx, depth + 1)) {
                return true;
            }
            this.sol.pop();
        }
        return false;
    };

    _.getSolArr = function () {
        var solArr = [];
        for (var i = 0; i < this.sol.length; i++) {
            solArr.push(this.movesList[this.sol[i]][0]);
        }
        return solArr;
    }

    var randGen = (function () {
        var rndFunc;
        var rndCnt;
        var seedStr; // '' + new Date().getTime();

        function random() {
            rndCnt++;
            // console.log(rndCnt);
            return rndFunc();
        }

        function getSeed() {
            return [rndCnt, seedStr];
        }

        function setSeed(_rndCnt, _seedStr) {
            if (_seedStr && (_seedStr != seedStr || rndCnt > _rndCnt)) {
                var seed = [];
                for (var i = 0; i < _seedStr.length; i++) {
                    seed[i] = _seedStr.charCodeAt(i);
                }
                rndFunc = new MersenneTwisterObject(seed[0], seed);
                rndCnt = 0;
                seedStr = _seedStr;
            }
            while (rndCnt < _rndCnt) {
                rndFunc();
                rndCnt++;
            }
        }

        // setSeed(0, '1576938267035');
        setSeed(0, '' + new Date().getTime());

        return {
            random: random,
            getSeed: getSeed,
            setSeed: setSeed
        };
    })();

    function rndEl(x) {
        return x[~~(randGen.random() * x.length)];
    }

    function rn(n) {
        return ~~(randGen.random() * n)
    }

    function rndPerm(n) {
        var arr = [];
        for (var i = 0; i < n; i++) {
            arr[i] = i;
        }
        for (var i = 0; i < n - 1; i++) {
            circle(arr, i, i + rn(n - i));
        }
        return arr;
    }

    function rndProb(plist) {
        var cum = 0;
        var curIdx = 0;
        for (var i = 0; i < plist.length; i++) {
            if (plist[i] == 0) {
                continue;
            }
            if (randGen.random() < plist[i] / (cum + plist[i])) {
                curIdx = i;
            }
            cum += plist[i];
        }
        return curIdx;
    }

    function time2str(unix, format) {
        if (!unix) {
            return 'N/A';
        }
        format = format || '%Y-%M-%D %h:%m:%s';
        var date = new Date(unix * 1000);
        return format
                .replace('%Y', date.getFullYear())
                .replace('%M', ('0' + (date.getMonth() + 1)).slice(-2))
                .replace('%D', ('0' + date.getDate()).slice(-2))
                .replace('%h', ('0' + date.getHours()).slice(-2))
                .replace('%m', ('0' + date.getMinutes()).slice(-2))
                .replace('%s', ('0' + date.getSeconds()).slice(-2));
    }

    var timeRe = /^\s*(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)\s*$/;

    function str2time(val) {
        var m = timeRe.exec(val);
        if (!m) {
            return null;
        }
        var date = new Date(0);
        date.setFullYear(~~m[1]);
        date.setMonth(~~m[2] - 1);
        date.setDate(~~m[3]);
        date.setHours(~~m[4]);
        date.setMinutes(~~m[5]);
        date.setSeconds(~~m[6]);
        return ~~(date.getTime() / 1000);
    }

    function obj2str(val) {
        if (typeof val == 'string') {
            return val;
        }
        return JSON.stringify(val);
    }

    function str2obj(val) {
        if (typeof val != 'string') {
            return val;
        }
        return JSON.parse(val);
    }

    function valuedArray(len, val) {
        var ret = [];
        for (var i = 0; i < len; i++) {
            ret[i] = val;
        }
        return ret;
    }

    Math.TAU = Math.PI * 2;

    return {
        Cnk: Cnk,
        fact: fact,
        getPruning: getPruning,
        setNPerm: setNPerm,
        getNPerm: getNPerm,
        getNParity: getNParity,
        get8Perm: get8Perm,
        set8Perm: set8Perm,
        coord: coord,
        createMove: createMove,
        edgeMove: edgeMove,
        circle: circle,
        circleOri: circleOri,
        acycle: acycle,
        createPrun: createPrun,
        CubieCube: CubieCube,
        SOLVED_FACELET: "UUUUUUUUURRRRRRRRRFFFFFFFFFDDDDDDDDDLLLLLLLLLBBBBBBBBB",
        fillFacelet: fillFacelet,
        rn: rn,
        rndEl: rndEl,
        rndProb: rndProb,
        time2str: time2str,
        str2time: str2time,
        obj2str: obj2str,
        str2obj: str2obj,
        valuedArray: valuedArray,
        Solver: Solver,
        rndPerm: rndPerm,
        gSolver: gSolver,
        getSeed: randGen.getSeed,
        setSeed: randGen.setSeed
    };
})();



/***************************************** image.js *********************************************/

function kernel_getProp(name) {
    return {
        "NTools": 1,
        "absidx": false,
        "ahide": true,
        "atexpa": "n",
        "atexpi": 100,
        "beepAt": "5,10,15,20",
        "beepEn": false,
        "bgImgO": 25,
        "bgImgS": "n",
        "col15p": "#f99#9f9#99f#fff",
        "col-back": "#eeffcc",
        "col-board": "#ffdddd",
        "col-button": "#ffbbbb",
        "col-font": "#000000",
        "col-link": "#0000ff",
        "col-logo": "#ffff00",
        "col-logoback": "#000000",
        "colcube": "#ff0#fa0#00f#fff#f00#0d0",
        "colmgm": "#fff#d00#060#81f#fc0#00b#ffb#8df#f83#7e0#f9f#999",
        "color": "1",
        "colpyr": "#0f0#f00#00f#ff0",
        "colskb": "#fff#00f#f00#ff0#0f0#f80",
        "colsq1": "#ff0#f80#0f0#fff#f00#00f",
        "delmul": true,
        "disPrec": "a",
        "expp": false,
        "font": "lcd",
        "giiAED": false,
        "giiBS": true,
        "giiRST": "p",
        "giiSD": "s",
        "giiSK": true,
        "giiSM": "n",
        "giiVRC": true,
        "imgSize": 15,
        "imrename": false,
        "input": "t",
        "intUN": 20100,
        "lang": "ru-ru",
        "phases": 1,
        "preScr": "",
        "preTime": 300,
        "printDate": false,
        "printScr": true,
        "rsfor1s": false,
        "scr2ss": false,
        "scrASize": true,
        "scrAlign": "c",
        "scrClk": "n",
        "scrFast": false,
        "scrFlt": "[\"333\",null]",
        "scrHide": false,
        "scrKeyM": false,
        "scrLim": false,
        "scrMono": true,
        "scrSize": 15,
        "scrType": "333",
        "scramble": true,
        "session": 1,
        "sessionData": "{}",
        "sessionN": 15,
        "showAvg": true,
        "smallADP": true,
        "solSpl": false,
        "sr_NTools": false,
        "sr_absidx": false,
        "sr_ahide": false,
        "sr_atexpa": false,
        "sr_atexpi": false,
        "sr_beepAt": false,
        "sr_beepEn": false,
        "sr_bgImgO": false,
        "sr_bgImgS": false,
        "sr_col15p": false,
        "sr_col-back": false,
        "sr_col-board": false,
        "sr_col-button": false,
        "sr_col-font": false,
        "sr_col-link": false,
        "sr_col-logo": false,
        "sr_col-logoback": false,
        "sr_colcube": false,
        "sr_colmgm": false,
        "sr_color": false,
        "sr_colpyr": false,
        "sr_colskb": false,
        "sr_colsq1": false,
        "sr_delmul": false,
        "sr_disPrec": false,
        "sr_expp": false,
        "sr_font": false,
        "sr_giiAED": false,
        "sr_giiBS": false,
        "sr_giiRST": false,
        "sr_giiSD": false,
        "sr_giiSK": false,
        "sr_giiSM": false,
        "sr_giiVRC": false,
        "sr_imgSize": false,
        "sr_imrename": false,
        "sr_input": false,
        "sr_intUN": false,
        "sr_lang": false,
        "sr_phases": true,
        "sr_preScr": false,
        "sr_preTime": false,
        "sr_printDate": false,
        "sr_printScr": false,
        "sr_rsfor1s": false,
        "sr_scr2ss": false,
        "sr_scrASize": false,
        "sr_scrAlign": false,
        "sr_scrClk": false,
        "sr_scrFast": false,
        "sr_scrFlt": false,
        "sr_scrHide": false,
        "sr_scrKeyM": false,
        "sr_scrLim": false,
        "sr_scrMono": false,
        "sr_scrSize": false,
        "sr_scrType": true,
        "sr_scramble": false,
        "sr_session": false,
        "sr_sessionData": false,
        "sr_sessionN": false,
        "sr_showAvg": false,
        "sr_smallADP": false,
        "sr_solSpl": false,
        "sr_stat1l": false,
        "sr_stat1t": false,
        "sr_stat2l": false,
        "sr_stat2t": false,
        "sr_statHide": false,
        "sr_statal": false,
        "sr_statalu": false,
        "sr_statclr": false,
        "sr_statinv": false,
        "sr_statsum": false,
        "sr_stkHead": false,
        "sr_timeFormat": false,
        "sr_timeU": false,
        "sr_timerSize": false,
        "sr_toolHide": false,
        "sr_tools": false,
        "sr_toolsfunc": false,
        "sr_trim": false,
        "sr_uidesign": false,
        "sr_useIns": false,
        "sr_useKSC": false,
        "sr_useLogo": false,
        "sr_useMilli": false,
        "sr_useMouse": false,
        "sr_view": false,
        "sr_voiceIns": false,
        "sr_voiceVol": false,
        "sr_vrcAH": false,
        "sr_vrcMP": false,
        "sr_vrcSpeed": false,
        "sr_wndScr": false,
        "sr_wndStat": false,
        "sr_wndTool": false,
        "sr_zoom": false,
        "stat1l": 5,
        "stat1t": 0,
        "stat2l": 12,
        "stat2t": 0,
        "statHide": false,
        "statal": "mo3 ao5 ao12 ao100",
        "statalu": "mo3 ao5 ao12 ao100",
        "statclr": true,
        "statinv": false,
        "stats": true,
        "statsrc": "t",
        "statsum": true,
        "stkHead": true,
        "timeFormat": "h",
        "timeU": "c",
        "timerSize": 20,
        "toolHide": false,
        "tools": false,
        "toolsfunc": "[\"image\",\"stats\",\"cross\",\"distribution\"]",
        "trim": "p5",
        "uidesign": "n",
        "useIns": "n",
        "useKSC": true,
        "useLogo": true,
        "useMilli": false,
        "useMouse": false,
        "view": "a",
        "voiceIns": "1",
        "voiceVol": 100,
        "vrcAH": "01",
        "vrcMP": "n",
        "vrcSpeed": 100,
        "wndScr": "n",
        "wndStat": "n",
        "wndTool": "n",
        "zoom": "1",
        'col-font': '#000000',
        'col-back': '#eeffcc',
        'col-board': '#ffdddd',
        'col-button': '#ffbbbb',
        'col-link': '#0000ff',
        'col-logo': '#ffff00',
        'col-logoback': '#000000',
        'colcube': '#ff0#fa0#00f#fff#f00#0d0',
        'colpyr': '#0f0#f00#00f#ff0',
        'colskb': '#fff#00f#f00#ff0#0f0#f80',
        'colmgm': '#fff#d00#060#81f#fc0#00b#ffb#8df#f83#7e0#f9f#999',
        'colsq1': '#ff0#f80#0f0#fff#f00#00f',
        'col15p': '#f99#9f9#99f#fff',
    }[name] || null;
}

function parseScramble(scramble, moveMap) {
    var scrambleReg = /^([\d]+)?([FRUBLDfrubldzxySME])(?:([w])|&sup([\d]);)?([2'])?$/;
    var moveseq = [];
    var moves = (kernel_getProp('preScr') + ' ' + scramble).split(' ');
    var m, w, f, p;
    for (var s = 0; s < moves.length; s++) {
        m = scrambleReg.exec(moves[s]);
        if (m == null) {
            continue;
        }
        f = "FRUBLDfrubldzxySME".indexOf(m[2]);
        if (f > 14) {
            p = "2'".indexOf(m[5] || 'X') + 2;
            f = [0, 4, 5][f % 3];
            moveseq.push([moveMap.indexOf("FRUBLD".charAt(f)), 2, p]);
            moveseq.push([moveMap.indexOf("FRUBLD".charAt(f)), 1, 4 - p]);
            continue;
        }
        w = f < 12 ? (~~m[1] || ~~m[4] || ((m[3] == "w" || f > 5) && 2) || 1) : -1;
        p = (f < 12 ? 1 : -1) * ("2'".indexOf(m[5] || 'X') + 2);
        moveseq.push([moveMap.indexOf("FRUBLD".charAt(f % 6)), w, p]);
    }
    return moveseq;
}


var image = execMain(function () {

    var canvas, ctx;
    var hsq3 = Math.sqrt(3) / 2;
    var PI = Math.PI;

    var Rotate = $.ctxRotate;
    var Transform = $.ctxTransform;
    var drawPolygon = $.ctxDrawPolygon;

    var mgmImage = (function () {
        var moveU = [4, 0, 1, 2, 3, 9, 5, 6, 7, 8, 10, 11, 12, 13, 58, 59, 16, 17, 18, 63, 20, 21, 22, 23, 24, 14, 15, 27, 28, 29, 19, 31, 32, 33, 34, 35, 25, 26, 38, 39, 40, 30, 42, 43, 44, 45, 46, 36, 37, 49, 50, 51, 41, 53, 54, 55, 56, 57, 47, 48, 60, 61, 62, 52, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131];
        var moveR = [81, 77, 78, 3, 4, 86, 82, 83, 8, 85, 87, 122, 123, 124, 125, 121, 127, 128, 129, 130, 126, 131, 89, 90, 24, 25, 88, 94, 95, 29, 97, 93, 98, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 26, 22, 23, 48, 30, 31, 27, 28, 53, 32, 69, 70, 66, 67, 68, 74, 75, 71, 72, 73, 76, 101, 102, 103, 99, 100, 106, 107, 108, 104, 105, 109, 46, 47, 79, 80, 45, 51, 52, 84, 49, 50, 54, 0, 1, 2, 91, 92, 5, 6, 7, 96, 9, 10, 15, 11, 12, 13, 14, 20, 16, 17, 18, 19, 21, 113, 114, 110, 111, 112, 118, 119, 115, 116, 117, 120, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65];
        var moveD = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 33, 34, 35, 14, 15, 38, 39, 40, 19, 42, 43, 44, 45, 46, 25, 26, 49, 50, 51, 30, 53, 54, 55, 56, 57, 36, 37, 60, 61, 62, 41, 64, 65, 11, 12, 13, 47, 48, 16, 17, 18, 52, 20, 21, 22, 23, 24, 58, 59, 27, 28, 29, 63, 31, 32, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 124, 125, 121, 122, 123, 129, 130, 126, 127, 128, 131];
        var moveMaps = [moveU, moveR, moveD];

        var width = 40;
        var cfrac = 0.5;
        var efrac2 = (Math.sqrt(5) + 1) / 2;
        var d2x = (1 - cfrac) / 2 / Math.tan(PI / 5);
        var off1X = 2.6;
        var off1Y = 2.2;
        var off2X = off1X + Math.cos(PI * 0.1) * 3 * efrac2;
        var off2Y = off1Y + Math.sin(PI * 0.1) * 1 * efrac2;
        var cornX = [0, d2x, 0, -d2x];
        var cornY = [-1, -(1 + cfrac) / 2, -cfrac, -(1 + cfrac) / 2];
        var edgeX = [Math.cos(PI * 0.1) - d2x, d2x, 0, Math.sin(PI * 0.4) * cfrac];
        var edgeY = [-Math.sin(PI * 0.1) + (cfrac - 1) / 2, -(1 + cfrac) / 2, -cfrac, -Math.cos(PI * 0.4) * cfrac];
        var centX = [Math.sin(PI * 0.0) * cfrac, Math.sin(PI * 0.4) * cfrac, Math.sin(PI * 0.8) * cfrac, Math.sin(PI * 1.2) * cfrac, Math.sin(PI * 1.6) * cfrac];
        var centY = [-Math.cos(PI * 0.0) * cfrac, -Math.cos(PI * 0.4) * cfrac, -Math.cos(PI * 0.8) * cfrac, -Math.cos(PI * 1.2) * cfrac, -Math.cos(PI * 1.6) * cfrac];
        var colors = ['#fff', '#d00', '#060', '#81f', '#fc0', '#00b', '#ffb', '#8df', '#f83', '#7e0', '#f9f', '#999'];

        function drawFace(state, baseIdx, trans, rot) {
            for (var i = 0; i < 5; i++) {
                drawPolygon(ctx, colors[state[baseIdx + i]], Rotate([cornX, cornY], PI * 2 / 5 * i + rot), trans);
                drawPolygon(ctx, colors[state[baseIdx + i + 5]], Rotate([edgeX, edgeY], PI * 2 / 5 * i + rot), trans);
            }
            drawPolygon(ctx, colors[state[baseIdx + 10]], Rotate([centX, centY], rot), trans);
        }

        function doMove(state, axis, inv) {
            var moveMap = moveMaps[axis];
            var oldState = state.slice();
            if (inv) {
                for (var i = 0; i < 132; i++) {
                    state[moveMap[i]] = oldState[i];
                }
            } else {
                for (var i = 0; i < 132; i++) {
                    state[i] = oldState[moveMap[i]];
                }
            }
        }

        var movere = /[RD][+-]{2}|U'?/
        return function (moveseq) {
            colors = kernel_getProp('colmgm').match(colre);
            var state = [];
            for (var i = 0; i < 12; i++) {
                for (var j = 0; j < 11; j++) {
                    state[i * 11 + j] = i;
                }
            }
            var moves = moveseq.split(/\s+/);
            for (var i = 0; i < moves.length; i++) {
                var m = movere.exec(moves[i]);
                if (!m) {
                    continue;
                }
                var axis = "URD".indexOf(m[0][0]);
                var inv = /[-']/.exec(m[0][1]);
                doMove(state, axis, inv);
            }
            var imgSize = kernel_getProp('imgSize') / 7.5;
            canvas.width(7 * imgSize + 'em');
            canvas.height(3.5 * imgSize + 'em');
            canvas.attr('width', 9.8 * width);
            canvas.attr('height', 4.9 * width);
            drawFace(state, 0, [width, off1X + 0 * efrac2, off1Y + 0 * efrac2], PI * 0.0);
            drawFace(state, 11, [width, off1X + Math.cos(PI * 0.1) * efrac2, off1Y + Math.sin(PI * 0.1) * efrac2], PI * 0.2);
            drawFace(state, 22, [width, off1X + Math.cos(PI * 0.5) * efrac2, off1Y + Math.sin(PI * 0.5) * efrac2], PI * 0.6);
            drawFace(state, 33, [width, off1X + Math.cos(PI * 0.9) * efrac2, off1Y + Math.sin(PI * 0.9) * efrac2], PI * 1.0);
            drawFace(state, 44, [width, off1X + Math.cos(PI * 1.3) * efrac2, off1Y + Math.sin(PI * 1.3) * efrac2], PI * 1.4);
            drawFace(state, 55, [width, off1X + Math.cos(PI * 1.7) * efrac2, off1Y + Math.sin(PI * 1.7) * efrac2], PI * 1.8);
            drawFace(state, 66, [width, off2X + Math.cos(PI * 0.7) * efrac2, off2Y + Math.sin(PI * 0.7) * efrac2], PI * 0.0);
            drawFace(state, 77, [width, off2X + Math.cos(PI * 0.3) * efrac2, off2Y + Math.sin(PI * 0.3) * efrac2], PI * 1.6);
            drawFace(state, 88, [width, off2X + Math.cos(PI * 1.9) * efrac2, off2Y + Math.sin(PI * 1.9) * efrac2], PI * 1.2);
            drawFace(state, 99, [width, off2X + Math.cos(PI * 1.5) * efrac2, off2Y + Math.sin(PI * 1.5) * efrac2], PI * 0.8);
            drawFace(state, 110, [width, off2X + Math.cos(PI * 1.1) * efrac2, off2Y + Math.sin(PI * 1.1) * efrac2], PI * 0.4);
            drawFace(state, 121, [width, off2X + 0 * efrac2, off2Y + 0 * efrac2], PI * 1.0);
            if (ctx) {
                ctx.fillStyle = "#000";
                ctx.font = "20px serif";
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText("U", width * off1X, width * off1Y);
                ctx.fillText("F", width * off1X, width * (off1Y + Math.sin(PI * 0.5) * efrac2));
            }
        };
    })();

    var clkImage = (function () {
        function drawClock(color, trans, time) {
            if (!ctx) {
                return;
            }
            var points = Transform(Rotate([
                [1, 1, 0, -1, -1, -1, 1, 0],
                [0, -1, -8, -1, 0, 1, 1, 0]
            ], time / 6 * PI), trans);
            var x = points[0];
            var y = points[1];

            ctx.beginPath();
            ctx.fillStyle = color;
            ctx.arc(x[7], y[7], trans[0] * 9, 0, 2 * PI);
            ctx.fill();

            ctx.beginPath();
            ctx.fillStyle = '#ff0';
            ctx.strokeStyle = '#f00';
            ctx.moveTo(x[0], y[0]);
            ctx.bezierCurveTo(x[1], y[1], x[1], y[1], x[2], y[2]);
            ctx.bezierCurveTo(x[3], y[3], x[3], y[3], x[4], y[4]);
            ctx.bezierCurveTo(x[5], y[5], x[6], y[6], x[0], y[0]);
            ctx.closePath();
            ctx.fill();
            ctx.stroke();
        }

        function drawButton(color, trans) {
            if (!ctx) {
                return;
            }
            var points = Transform([
                [0],
                [0]
            ], trans);
            ctx.beginPath();
            ctx.fillStyle = color;
            ctx.strokeStyle = '#000';
            ctx.arc(points[0][0], points[1][0], trans[0] * 3, 0, 2 * PI);
            ctx.fill();
            ctx.stroke();
        }

        var width = 3;
        var movere = /([UD][RL]|ALL|[UDRLy])(\d[+-]?)?/
        var movestr = ['UR', 'DR', 'DL', 'UL', 'U', 'R', 'D', 'L', 'ALL']

        return function (moveseq) {
            var moves = moveseq.split(/\s+/);
            var moveArr = [
                [0, 1, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0],
                [1, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 1, 1, 0, 1, 1, 0, 1, 1, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
                [1, 1, 0, 1, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0],
                [1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
                [11, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 1, 0],
                [0, 0, 0, 0, 0, 0, 11, 0, 0, 0, 0, 1, 1, 1],
                [0, 0, 0, 0, 0, 0, 0, 0, 11, 0, 1, 1, 0, 1],
                [0, 0, 11, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 0],
                [11, 0, 11, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0],
                [11, 0, 0, 0, 0, 0, 11, 0, 0, 1, 0, 1, 1, 1],
                [0, 0, 0, 0, 0, 0, 11, 0, 11, 0, 1, 1, 1, 1],
                [0, 0, 11, 0, 0, 0, 0, 0, 11, 1, 1, 1, 0, 1],
                [11, 0, 11, 0, 0, 0, 11, 0, 11, 1, 1, 1, 1, 1]
            ];
            var flip = 9;
            var buttons = [0, 0, 0, 0];
            var clks = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            for (var i = 0; i < moves.length; i++) {
                var m = movere.exec(moves[i]);
                if (!m) {
                    continue;
                }
                if (m[0] == 'y2') {
                    flip = 0;
                    continue;
                }
                var axis = movestr.indexOf(m[1]) + flip;
                if (m[2] == undefined) {
                    buttons[axis % 9] = 1;
                    continue;
                }
                var power = ~~m[2][0];
                power = m[2][1] == '+' ? power : 12 - power;
                for (var j = 0; j < 14; j++) {
                    clks[j] = (clks[j] + moveArr[axis][j] * power) % 12;
                }
            }
            clks = [clks[0], clks[3], clks[6], clks[1], clks[4], clks[7], clks[2], clks[5], clks[8],
                12 - clks[2], clks[10], 12 - clks[8], clks[9], clks[11], clks[13], 12 - clks[0], clks[12], 12 - clks[6]
            ];
            buttons = [buttons[3], buttons[2], buttons[0], buttons[1], 1 - buttons[0], 1 - buttons[1], 1 - buttons[3], 1 - buttons[2]];

            var imgSize = kernel_getProp('imgSize') / 7.5;
            canvas.width(6.25 * imgSize + 'em');
            canvas.height(3 * imgSize + 'em');
            canvas.attr('width', 6.25 * 20 * width);
            canvas.attr('height', 3 * 20 * width);

            var y = [10, 30, 50];
            var x = [10, 30, 50, 75, 95, 115];
            for (var i = 0; i < 18; i++) {
                drawClock(['#37b', '#5cf'][~~(i / 9)], [width, x[~~(i / 3)], y[i % 3]], clks[i]);
            }

            var y = [20, 40];
            var x = [20, 40, 85, 105];
            for (var i = 0; i < 8; i++) {
                drawButton(['#850', '#ff0'][buttons[i]], [width, x[~~(i / 2)], y[i % 2]]);
            }
        };
    })();


    var sq1Image = (function () {
        var posit = [];
        var mid = 0;

        //(move[0], move[1]) (/ = move[2])
        function doMove(move) {
            var newposit = [];

            //top move
            for (var i = 0; i < 12; i++) {
                newposit[(i + move[0]) % 12] = posit[i];
            }

            //bottom move
            for (var i = 0; i < 12; i++) {
                newposit[i + 12] = posit[(i + move[1]) % 12 + 12];
            }

            if (move[2]) {
                mid = 1 - mid;
                for (var i = 0; i < 6; i++) {
                    mathlib.circle(newposit, i + 6, 23 - i);
                }
            }
            posit = newposit;
        }

        var ep = [
            [0, -0.5, 0.5],
            [0, -hsq3 - 1, -hsq3 - 1]
        ];
        var cp = [
            [0, -0.5, -hsq3 - 1, -hsq3 - 1],
            [0, -hsq3 - 1, -hsq3 - 1, -0.5]
        ];
        var cpr = [
            [0, -0.5, -hsq3 - 1],
            [0, -hsq3 - 1, -hsq3 - 1]
        ];
        var cpl = [
            [0, -hsq3 - 1, -hsq3 - 1],
            [0, -hsq3 - 1, -0.5]
        ];

        var eps = Transform(ep, [0.66, 0, 0]);
        var cps = Transform(cp, [0.66, 0, 0]);

        var udcol = 'UD';
        var ecol = '-B-R-F-L-B-R-F-L';
        var ccol = 'LBBRRFFLBLRBFRLF';
        var colors = {
            'U': '#ff0',
            'R': '#f80',
            'F': '#0f0',
            'D': '#fff',
            'L': '#f00',
            'B': '#00f'
        };

        var width = 45;

        var movere = /^\s*\(\s*(-?\d+),\s*(-?\d+)\s*\)\s*$/

        return function (moveseq) {
            var cols = kernel_getProp('colsq1').match(colre);
            colors = {
                'U': cols[0],
                'R': cols[1],
                'F': cols[2],
                'D': cols[3],
                'L': cols[4],
                'B': cols[5]
            };
            posit = [0, 0, 1, 2, 2, 3, 4, 4, 5, 6, 6, 7, 8, 8, 9, 10, 10, 11, 12, 12, 13, 14, 14, 15];
            mid = 0;
            var moves = moveseq.split('/');
            for (var i = 0; i < moves.length; i++) {
                if (/^\s*$/.exec(moves[i])) {
                    doMove([0, 0, 1]);
                    continue;
                }
                var m = movere.exec(moves[i]);
                doMove([~~m[1] + 12, ~~m[2] + 12, 1]);
            }
            doMove([0, 0, 1]);


            var imgSize = kernel_getProp('imgSize') / 10;
            canvas.width(11 * imgSize / 1.3 + 'em');
            canvas.height(6.3 * imgSize / 1.3 + 'em');

            canvas.attr('width', 11 * width);
            canvas.attr('height', 6.3 * width);

            var trans = [width, 2.7, 2.7];
            //draw top
            for (var i = 0; i < 12; i++) {
                if (posit[i] % 2 == 0) { //corner piece
                    if (posit[i] != posit[(i + 1) % 12]) {
                        continue;
                    }
                    drawPolygon(ctx, colors[ccol[posit[i]]],
                            Rotate(cpl, (i - 3) * PI / 6), trans);
                    drawPolygon(ctx, colors[ccol[posit[i] + 1]],
                            Rotate(cpr, (i - 3) * PI / 6), trans);
                    drawPolygon(ctx, colors[udcol[posit[i] >= 8 ? 1 : 0]],
                            Rotate(cps, (i - 3) * PI / 6), trans);
                } else { //edge piece
                    drawPolygon(ctx, colors[ecol[posit[i]]],
                            Rotate(ep, (i - 5) * PI / 6), trans);
                    drawPolygon(ctx, colors[udcol[posit[i] >= 8 ? 1 : 0]],
                            Rotate(eps, (i - 5) * PI / 6), trans);
                }
            }

            var trans = [width, 2.7 + 5.4, 2.7];
            //draw bottom
            for (var i = 12; i < 24; i++) {
                if (posit[i] % 2 == 0) { //corner piece
                    if (posit[i] != posit[(i + 1) % 12 + 12]) {
                        continue;
                    }
                    drawPolygon(ctx, colors[ccol[posit[i]]],
                            Rotate(cpl, -i * PI / 6), trans);
                    drawPolygon(ctx, colors[ccol[posit[i] + 1]],
                            Rotate(cpr, -i * PI / 6), trans);
                    drawPolygon(ctx, colors[udcol[posit[i] >= 8 ? 1 : 0]],
                            Rotate(cps, -i * PI / 6), trans);
                } else { //edge piece
                    drawPolygon(ctx, colors[ecol[posit[i]]],
                            Rotate(ep, (-1 - i) * PI / 6), trans);
                    drawPolygon(ctx, colors[udcol[posit[i] >= 8 ? 1 : 0]],
                            Rotate(eps, (-1 - i) * PI / 6), trans);
                }
            }

            var trans = [width, 2.7 + 2.7, 2.7 + 3.0];
            //draw middle
            drawPolygon(ctx, colors['L'], [
                [-hsq3 - 1, -hsq3 - 1, -0.5, -0.5],
                [0.5, -0.5, -0.5, 0.5]
            ], trans);
            if (mid == 0) {
                drawPolygon(ctx, colors['L'], [
                    [hsq3 + 1, hsq3 + 1, -0.5, -0.5],
                    [0.5, -0.5, -0.5, 0.5]
                ], trans);
            } else {
                drawPolygon(ctx, colors['R'], [
                    [hsq3, hsq3, -0.5, -0.5],
                    [0.5, -0.5, -0.5, 0.5]
                ], trans);
            }
        }
    })();

    var skewbImage = (function () {
        var width = 45;
        var gap = width / 10;
        var posit = [];
        var colors = ['#fff', '#00f', '#f00', '#ff0', '#0f0', '#f80'];

        var ftrans = [
            [width * hsq3, width * hsq3, (width * 4 + gap * 1.5) * hsq3, -width / 2, width / 2, width],
            [width * hsq3, 0, (width * 7 + gap * 3) * hsq3, -width / 2, width, width * 1.5],
            [width * hsq3, 0, (width * 5 + gap * 2) * hsq3, -width / 2, width, width * 2.5 + 0.5 * gap],
            [0, -width * hsq3, (width * 3 + gap * 1) * hsq3, width, -width / 2, width * 4.5 + 1.5 * gap],
            [width * hsq3, 0, (width * 3 + gap * 1) * hsq3, width / 2, width, width * 2.5 + 0.5 * gap],
            [width * hsq3, 0, width * hsq3, width / 2, width, width * 1.5]
        ];

        function doMove(axis, power) {
            for (var p = 0; p < power; p++) {
                switch (axis) {
                    case 0: //R
                        mathlib.circle(posit, 2 * 5 + 0, 1 * 5 + 0, 3 * 5 + 0);
                        mathlib.circle(posit, 2 * 5 + 4, 1 * 5 + 3, 3 * 5 + 2);
                        mathlib.circle(posit, 2 * 5 + 2, 1 * 5 + 4, 3 * 5 + 1);
                        mathlib.circle(posit, 2 * 5 + 3, 1 * 5 + 1, 3 * 5 + 4);
                        mathlib.circle(posit, 4 * 5 + 4, 0 * 5 + 4, 5 * 5 + 3);
                        break;
                    case 1: //U
                        mathlib.circle(posit, 0 * 5 + 0, 5 * 5 + 0, 1 * 5 + 0);
                        mathlib.circle(posit, 0 * 5 + 2, 5 * 5 + 1, 1 * 5 + 2);
                        mathlib.circle(posit, 0 * 5 + 4, 5 * 5 + 2, 1 * 5 + 4);
                        mathlib.circle(posit, 0 * 5 + 1, 5 * 5 + 3, 1 * 5 + 1);
                        mathlib.circle(posit, 4 * 5 + 1, 3 * 5 + 4, 2 * 5 + 2);
                        break;
                    case 2: //L
                        mathlib.circle(posit, 4 * 5 + 0, 3 * 5 + 0, 5 * 5 + 0);
                        mathlib.circle(posit, 4 * 5 + 3, 3 * 5 + 3, 5 * 5 + 4);
                        mathlib.circle(posit, 4 * 5 + 1, 3 * 5 + 1, 5 * 5 + 3);
                        mathlib.circle(posit, 4 * 5 + 4, 3 * 5 + 4, 5 * 5 + 2);
                        mathlib.circle(posit, 2 * 5 + 3, 1 * 5 + 4, 0 * 5 + 1);
                        break;
                    case 3: //B
                        mathlib.circle(posit, 1 * 5 + 0, 5 * 5 + 0, 3 * 5 + 0);
                        mathlib.circle(posit, 1 * 5 + 4, 5 * 5 + 3, 3 * 5 + 4);
                        mathlib.circle(posit, 1 * 5 + 3, 5 * 5 + 1, 3 * 5 + 3);
                        mathlib.circle(posit, 1 * 5 + 2, 5 * 5 + 4, 3 * 5 + 2);
                        mathlib.circle(posit, 0 * 5 + 2, 4 * 5 + 3, 2 * 5 + 4);
                        break;
                }
            }
        }

        function face(f) {
            var transform = ftrans[f];
            drawPolygon(ctx, colors[posit[f * 5 + 0]], [
                [-1, 0, 1, 0],
                [0, 1, 0, -1]
            ], transform);
            drawPolygon(ctx, colors[posit[f * 5 + 1]], [
                [-1, -1, 0],
                [0, -1, -1]
            ], transform);
            drawPolygon(ctx, colors[posit[f * 5 + 2]], [
                [0, 1, 1],
                [-1, -1, 0]
            ], transform);
            drawPolygon(ctx, colors[posit[f * 5 + 3]], [
                [-1, -1, 0],
                [0, 1, 1]
            ], transform);
            drawPolygon(ctx, colors[posit[f * 5 + 4]], [
                [0, 1, 1],
                [1, 1, 0]
            ], transform);
        }

        return function (moveseq) {
            colors = kernel_getProp('colskb').match(colre);
            var cnt = 0;
            for (var i = 0; i < 6; i++) {
                for (var f = 0; f < 5; f++) {
                    posit[cnt++] = i;
                }
            }
            var scramble = parseScramble(moveseq, 'RULB');
            for (var i = 0; i < scramble.length; i++) {
                doMove(scramble[i][0], scramble[i][2] == 1 ? 1 : 2);
            }
            var imgSize = kernel_getProp('imgSize') / 10;
            canvas.width((8 * hsq3 + 0.3) * imgSize + 'em');
            canvas.height(6.2 * imgSize + 'em');

            canvas.attr('width', (8 * hsq3 + 0.3) * width + 1);
            canvas.attr('height', 6.2 * width + 1);

            for (var i = 0; i < 6; i++) {
                face(i);
            }
        }
    })();

    /*
     
     face:   
     1 0 2
     3
     
     posit: 
     2 8 3 7 1    0    2 8 3 7 1
     4 6 5    5 6 4    4 6 5  
     0    1 7 3 8 2    0    
     
     2 8 3 7 1
     4 6 5  
     0    
     
     */

    var pyraImage = (function () {
        var width = 45;
        var posit = [];
        var colors = ['#0f0', '#f00', '#00f', '#ff0'];
        var faceoffx = [3.5, 1.5, 5.5, 3.5];
        var faceoffy = [0, 3 * hsq3, 3 * hsq3, 6.5 * hsq3];
        var g1 = [0, 6, 5, 4];
        var g2 = [1, 7, 3, 5];
        var g3 = [2, 8, 4, 3];
        var flist = [
            [0, 1, 2],
            [2, 3, 0],
            [1, 0, 3],
            [3, 2, 1]
        ];
        var arrx = [-0.5, 0.5, 0];
        var arry1 = [hsq3, hsq3, 0];
        var arry2 = [-hsq3, -hsq3, 0];

        function doMove(axis, power) {
            var len = axis >= 4 ? 1 : 4;
            var f = flist[axis % 4];
            for (var i = 0; i < len; i++) {
                for (var p = 0; p < power; p++) {
                    mathlib.circle(posit, f[0] * 9 + g1[i], f[1] * 9 + g2[i], f[2] * 9 + g3[i]);
                }
            }
        }

        function face(f) {
            var inv = f != 0;
            var arroffx = [0, -1, 1, 0, 0.5, -0.5, 0, -0.5, 0.5];
            var arroffy = [0, 2, 2, 2, 1, 1, 2, 3, 3];

            for (var i = 0; i < arroffy.length; i++) {
                arroffy[i] *= inv ? -hsq3 : hsq3;
                arroffx[i] *= inv ? -1 : 1;
            }
            for (var idx = 0; idx < 9; idx++) {
                drawPolygon(ctx, colors[posit[f * 9 + idx]], [arrx, (idx >= 6 != inv) ? arry2 : arry1], [width, faceoffx[f] + arroffx[idx], faceoffy[f] + arroffy[idx]]);
            }
        }

        return function (moveseq) {
            colors = kernel_getProp('colpyr').match(colre);
            var cnt = 0;
            for (var i = 0; i < 4; i++) {
                for (var f = 0; f < 9; f++) {
                    posit[cnt++] = i;
                }
            }
            var scramble = parseScramble(moveseq, 'URLB');
            for (var i = 0; i < scramble.length; i++) {
                doMove(scramble[i][0] + (scramble[i][1] == 2 ? 4 : 0), scramble[i][2] == 1 ? 1 : 2);
            }
            var imgSize = kernel_getProp('imgSize') / 10;
            canvas.width(7 * imgSize + 'em');
            canvas.height(6.5 * hsq3 * imgSize + 'em');

            canvas.attr('width', 7 * width);
            canvas.attr('height', 6.5 * hsq3 * width);

            for (var i = 0; i < 4; i++) {
                face(i);
            }
        }
    })();

    var nnnImage = (function () {
        var width = 30;

        var posit = [];
        var colors = ['#ff0', '#fa0', '#00f', '#fff', '#f00', '#0d0'];

        function face(f, size) {
            var offx = 10 / 9,
                    offy = 10 / 9;
            if (f == 0) { //D
                offx *= size;
                offy *= size * 2;
            } else if (f == 1) { //L
                offx *= 0;
                offy *= size;
            } else if (f == 2) { //B
                offx *= size * 3;
                offy *= size;
            } else if (f == 3) { //U
                offx *= size;
                offy *= 0;
            } else if (f == 4) { //R
                offx *= size * 2;
                offy *= size;
            } else if (f == 5) { //F
                offx *= size;
                offy *= size;
            }

            for (var i = 0; i < size; i++) {
                var x = (f == 1 || f == 2) ? size - 1 - i : i;
                for (var j = 0; j < size; j++) {
                    var y = (f == 0) ? size - 1 - j : j;
                    drawPolygon(ctx, colors[posit[(f * size + y) * size + x]], [
                        [i, i, i + 1, i + 1],
                        [j, j + 1, j + 1, j]
                    ], [width, offx, offy]);
                }
            }
        }

        /**
         *  f: face, [ D L B U R F ]
         *  d: which slice, in [0, size-1)
         *  q: [  2 ']
         */
        function doslice(f, d, q, size) {
            var f1, f2, f3, f4;
            var s2 = size * size;
            var c, i, j, k;
            if (f > 5)
                f -= 6;
            for (k = 0; k < q; k++) {
                for (i = 0; i < size; i++) {
                    if (f == 0) {
                        f1 = 6 * s2 - size * d - size + i;
                        f2 = 2 * s2 - size * d - 1 - i;
                        f3 = 3 * s2 - size * d - 1 - i;
                        f4 = 5 * s2 - size * d - size + i;
                    } else if (f == 1) {
                        f1 = 3 * s2 + d + size * i;
                        f2 = 3 * s2 + d - size * (i + 1);
                        f3 = s2 + d - size * (i + 1);
                        f4 = 5 * s2 + d + size * i;
                    } else if (f == 2) {
                        f1 = 3 * s2 + d * size + i;
                        f2 = 4 * s2 + size - 1 - d + size * i;
                        f3 = d * size + size - 1 - i;
                        f4 = 2 * s2 - 1 - d - size * i;
                    } else if (f == 3) {
                        f1 = 4 * s2 + d * size + size - 1 - i;
                        f2 = 2 * s2 + d * size + i;
                        f3 = s2 + d * size + i;
                        f4 = 5 * s2 + d * size + size - 1 - i;
                    } else if (f == 4) {
                        f1 = 6 * s2 - 1 - d - size * i;
                        f2 = size - 1 - d + size * i;
                        f3 = 2 * s2 + size - 1 - d + size * i;
                        f4 = 4 * s2 - 1 - d - size * i;
                    } else if (f == 5) {
                        f1 = 4 * s2 - size - d * size + i;
                        f2 = 2 * s2 - size + d - size * i;
                        f3 = s2 - 1 - d * size - i;
                        f4 = 4 * s2 + d + size * i;
                    }
                    c = posit[f1];
                    posit[f1] = posit[f2];
                    posit[f2] = posit[f3];
                    posit[f3] = posit[f4];
                    posit[f4] = c;
                }
                if (d == 0) {
                    for (i = 0; i + i < size; i++) {
                        for (j = 0; j + j < size - 1; j++) {
                            f1 = f * s2 + i + j * size;
                            f3 = f * s2 + (size - 1 - i) + (size - 1 - j) * size;
                            if (f < 3) {
                                f2 = f * s2 + (size - 1 - j) + i * size;
                                f4 = f * s2 + j + (size - 1 - i) * size;
                            } else {
                                f4 = f * s2 + (size - 1 - j) + i * size;
                                f2 = f * s2 + j + (size - 1 - i) * size;
                            }
                            c = posit[f1];
                            posit[f1] = posit[f2];
                            posit[f2] = posit[f3];
                            posit[f3] = posit[f4];
                            posit[f4] = c;
                        }
                    }
                }
            }
        }

        return function (size, moveseq) {
            colors = kernel_getProp('colcube').match(colre);
            var cnt = 0;
            for (var i = 0; i < 6; i++) {
                for (var f = 0; f < size * size; f++) {
                    posit[cnt++] = i;
                }
            }
            var moves = parseScramble(moveseq, "DLBURF");
            for (var s = 0; s < moves.length; s++) {
                for (var d = 0; d < moves[s][1]; d++) {
                    doslice(moves[s][0], d, moves[s][2], size)
                }
                if (moves[s][1] == -1) {
                    for (var d = 0; d < size - 1; d++) {
                        doslice(moves[s][0], d, -moves[s][2], size);
                    }
                    doslice((moves[s][0] + 3) % 6, 0, moves[s][2] + 4, size);
                }
            }

            var imgSize = kernel_getProp('imgSize') / 50;
            canvas.width(39 * imgSize + 'em');
            canvas.height(29 * imgSize + 'em');

            canvas.attr('width', 39 * size / 9 * width + 1);
            canvas.attr('height', 29 * size / 9 * width + 1);

            for (var i = 0; i < 6; i++) {
                face(i, size);
            }
        }
    })();

    var sldImage = (function () {

        return function (type, size, moveseq) {
            var width = 50;
            var gap = 0.05;

            var state = [];
            var effect = [
                [1, 0],
                [0, 1],
                [0, -1],
                [-1, 0]
            ];
            for (var i = 0; i < size * size; i++) {
                state[i] = i;
            }
            var x = size - 1;
            var y = size - 1;

            var movere = /([ULRD\uFFEA\uFFE9\uFFEB\uFFEC])([\d]?)/;
            moveseq = moveseq.split(' ');
            // console.log(moves.findall)
            for (var s = 0; s < moveseq.length; s++) {
                var m = movere.exec(moveseq[s]);
                if (!m) {
                    continue;
                }
                var turn = 'ULRD\uFFEA\uFFE9\uFFEB\uFFEC'.indexOf(m[1]) % 4;
                var pow = ~~m[2] || 1;
                var eff = effect[type == 'b' ? 3 - turn : turn];
                for (var p = 0; p < pow; p++) {
                    mathlib.circle(state, x * size + y, (x + eff[0]) * size + y + eff[1]);
                    x += eff[0];
                    y += eff[1];
                }
            }

            var imgSize = kernel_getProp('imgSize') / 50;
            canvas.width(30 * imgSize + 'em');
            canvas.height(30 * imgSize + 'em');

            canvas.attr('width', (size + gap * 4) * width);
            canvas.attr('height', (size + gap * 4) * width);

            var cols = kernel_getProp('col15p').match(colre);
            cols[size - 1] = cols[cols.length - 1];
            for (var i = 0; i < size; i++) {
                for (var j = 0; j < size; j++) {
                    var val = state[j * size + i];
                    var colorIdx = Math.min(~~(val / size), val % size);
                    val++;
                    drawPolygon(ctx, cols[colorIdx], [
                        [i + gap, i + gap, i + 1 - gap, i + 1 - gap],
                        [j + gap, j + 1 - gap, j + 1 - gap, j + gap]
                    ], [width, gap * 2, gap * 2]);
                    if (val == size * size) {
                        continue;
                    }
                    ctx.fillStyle = "#000";
                    ctx.font = width * 0.6 + "px monospace";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "middle";
                    ctx.fillText(val, width * (i + 0.5 + gap * 2), width * (j + 0.5 + gap * 2));
                }
            }
        }
    })();


    var types_nnn = ['', '', '222', '333', '444', '555', '666', '777', '888', '999', '101010', '111111'];

    function genImage(scramble) {
        canvas = $('<canvas>');
        ctx = canvas[0].getContext('2d');

        var type = scramble[0];
        if (type == 'input') {
            type = tools.scrambleType(scramble[1]);
        }
        type = tools.puzzleType(type);
        var size;
        for (size = 0; size < 12; size++) {
            if (type == types_nnn[size]) {
                nnnImage(size, scramble[1]);
                return canvas;
            }
        }
        if (type == "cubennn") {
            nnnImage(scramble[2], scramble[1]);
            return canvas;
        }
        if (type == "pyr") {
            pyraImage(scramble[1]);
            return canvas;
        }
        if (type == "skb") {
            skewbImage(scramble[1]);
            return canvas;
        }
        if (type == "sq1") {
            sq1Image(scramble[1]);
            return canvas;
        }
        if (type == "clk") {
            clkImage(scramble[1]);
            return canvas;
        }
        if (type == "mgm") {
            console.log('MGM');
            mgmImage(scramble[1]);
            return canvas;
        }
        if (type == "15b" || type == "15p") {
            sldImage(type[2], 4, scramble[1]);
            return canvas;
        }
        if (type == "8b" || type == "8p") {
            sldImage(type[1], 3, scramble[1]);
            return canvas;
        }
        return false;
    }

    var colre = /#[0-9a-fA-F]{3}/g;

    return {
        draw: genImage
    }
});