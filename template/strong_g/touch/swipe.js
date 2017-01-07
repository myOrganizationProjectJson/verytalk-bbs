(function(){var A;A=function(){var D,C,B;C=[];D=function(F,E){var G;if(!F){return B()}else{if(D.toType(F)==="function"){return D(document).ready(F)}else{G=D.getDOMObject(F,E);return B(G,F)}}};B=function(F,E){F=F||C;F.__proto__=B.prototype;F.selector=E||"";return F};D.extend=function(E){Array.prototype.slice.call(arguments,1).forEach(function(H){var G,F;F=[];for(G in H){F.push(E[G]=H[G])}return F});return E};B.prototype=D.fn={};return D}();window.Quo=A;"$$" in window||(window.$$=A)}).call(this);(function(){(function(G){var K,C,I,A,L,E,D,J,F,H,B;K={TYPE:"GET",MIME:"json"};I={script:"text/javascript, application/javascript",json:"application/json",xml:"application/xml, text/xml",html:"text/html",text:"text/plain"};C=0;G.ajaxSettings={type:K.TYPE,async:true,success:{},error:{},context:null,dataType:K.MIME,headers:{},xhr:function(){return new window.XMLHttpRequest},crossDomain:false,timeout:0};G.ajax=function(N){var M,O,R,P;R=G.mix(G.ajaxSettings,N);if(R.type===K.TYPE){R.url+=G.serializeParameters(R.data,"?")}else{R.data=G.serializeParameters(R.data)}if(A(R.url)){return G.jsonp(R)}P=R.xhr();P.onreadystatechange=function(){if(P.readyState===4){clearTimeout(M);return F(P,R)}};P.open(R.type,R.url,R.async);J(P,R);if(R.timeout>0){M=setTimeout(function(){return B(P,R)},R.timeout)}try{P.send(R.data)}catch(Q){O=Q;P=O;E("Resource not found",P,R)}if(R.async){return P}else{return L(P,R)}};G.jsonp=function(N){var M,Q,O,P;if(N.async){Q="jsonp"+ ++C;O=document.createElement("script");P={abort:function(){G(O).remove();if(Q in window){return window[Q]={}}}};M=void 0;window[Q]=function(R){clearTimeout(M);G(O).remove();delete window[Q];return H(R,P,N)};O.src=N.url.replace(RegExp("=\\?"),"="+Q);G("head").append(O);if(N.timeout>0){M=setTimeout(function(){return B(P,N)},N.timeout)}return P}else{return console.error("QuoJS.ajax: Unable to make jsonp synchronous call.")}};G.get=function(O,N,M,P){return G.ajax({url:O,data:N,success:M,dataType:P})};G.post=function(P,O,N,M){return D("POST",P,O,N,M)};G.put=function(P,O,N,M){return D("PUT",P,O,N,M)};G["delete"]=function(P,O,N,M){return D("DELETE",P,O,N,M)};G.json=function(N,M,O){return G.ajax({url:N,data:M,success:O,dataType:K.MIME})};G.serializeParameters=function(P,O){var N,M;if(O==null){O=""}M=O;for(N in P){if(P.hasOwnProperty(N)){if(M!==O){M+="&"}M+=""+encodeURIComponent(N)+"="+encodeURIComponent(P[N])}}if(M===O){return""}else{return M}};F=function(N,M){if(N.status>=200&&N.status<300||N.status===0){if(M.async){H(L(N,M),N,M)}}else{E("QuoJS.ajax: Unsuccesful request",N,M)}};H=function(O,N,M){M.success.call(M.context,O,N)};E=function(O,N,M){M.error.call(M.context,O,N,M)};J=function(O,N){var M;if(N.contentType){N.headers["Content-Type"]=N.contentType}if(N.dataType){N.headers["Accept"]=I[N.dataType]}for(M in N.headers){O.setRequestHeader(M,N.headers[M])}};B=function(N,M){N.onreadystatechange={};N.abort();E("QuoJS.ajax: Timeout exceeded",N,M)};D=function(O,N,M,Q,P){return G.ajax({type:O,url:N,data:M,success:Q,dataType:P,contentType:"application/x-www-form-urlencoded"})};L=function(Q,N){var M,P;P=Q.responseText;if(P){if(N.dataType===K.MIME){try{P=JSON.parse(P)}catch(O){M=O;P=M;E("QuoJS.ajax: Parse Error",Q,N)}}else{if(N.dataType==="xml"){P=Q.responseXML}}}return P};return A=function(M){return RegExp("=\\?").test(M)}})(Quo)}).call(this);(function(){(function(E){var H,B,F,A,I,D,C,G;H=[];A=Object.prototype;F=/^\s*<(\w+|!)[^>]*>/;I=document.createElement("table");D=document.createElement("tr");B={tr:document.createElement("tbody"),tbody:I,thead:I,tfoot:I,td:D,th:D,"*":document.createElement("div")};E.toType=function(J){return A.toString.call(J).match(/\s([a-z|A-Z]+)/)[1].toLowerCase()};E.isOwnProperty=function(K,J){return A.hasOwnProperty.call(K,J)};E.getDOMObject=function(K,J){var N,L,M;N=null;L=[1,9,11];M=E.toType(K);if(M==="array"){N=C(K)}else{if(M==="string"&&F.test(K)){N=E.fragment(K.trim(),RegExp.$1);K=null}else{if(M==="string"){N=E.query(document,K);if(J){if(N.length===1){N=E.query(N[0],J)}else{N=E.map(function(){return E.query(N,J)})}}}else{if(L.indexOf(K.nodeType)>=0||K===window){N=[K];K=null}}}}return N};E.map=function(L,K){var J,O,M,N;N=[];J=void 0;O=void 0;if(E.toType(L)==="array"){J=0;while(J<L.length){M=K(L[J],J);if(M!=null){N.push(M)}J++}}else{for(O in L){M=K(L[O],O);if(M!=null){N.push(M)}}}return G(N)};E.each=function(L,K){var J,M;J=void 0;M=void 0;if(E.toType(L)==="array"){J=0;while(J<L.length){if(K.call(L[J],J,L[J])===false){return L}J++}}else{for(M in L){if(K.call(L[M],M,L[M])===false){return L}}}return L};E.mix=function(){var L,K,J,N,M;J={};L=0;N=arguments.length;while(L<N){K=arguments[L];for(M in K){if(E.isOwnProperty(K,M)&&K[M]!==undefined){J[M]=K[M]}}L++}return J};E.fragment=function(K,J){var L;if(J==null){J="*"}if(!(J in B)){J="*"}L=B[J];L.innerHTML=""+K;return E.each(Array.prototype.slice.call(L.childNodes),function(){return L.removeChild(this)})};E.fn.map=function(J){return E.map(this,function(L,K){return J.call(L,K,L)})};E.fn.instance=function(J){return this.map(function(){return this[J]})};E.fn.filter=function(J){return E([].filter.call(this,function(K){return K.parentNode&&E.query(K.parentNode,J).indexOf(K)>=0}))};E.fn.forEach=H.forEach;E.fn.indexOf=H.indexOf;C=function(J){return J.filter(function(K){return K!==void 0&&K!==null})};return G=function(J){if(J.length>0){return[].concat.apply([],J)}else{return J}}})(Quo)}).call(this);(function(){(function(A){A.fn.attr=function(C,B){if(this.length===0){null}if(A.toType(C)==="string"&&B===void 0){return this[0].getAttribute(C)}else{return this.each(function(){return this.setAttribute(C,B)})}};A.fn.removeAttr=function(B){return this.each(function(){return this.removeAttribute(B)})};A.fn.data=function(C,B){return this.attr("data-"+C,B)};A.fn.removeData=function(B){return this.removeAttr("data-"+B)};A.fn.val=function(B){if(A.toType(B)==="string"){return this.each(function(){return this.value=B})}else{if(this.length>0){return this[0].value}else{return null}}};A.fn.show=function(){return this.style("display","block")};A.fn.hide=function(){return this.style("display","none")};A.fn.height=function(){var B;B=this.offset();return B.height};A.fn.width=function(){var B;B=this.offset();return B.width};A.fn.offset=function(){var B;B=this[0].getBoundingClientRect();return{left:B.left+window.pageXOffset,top:B.top+window.pageYOffset,width:B.width,height:B.height}};return A.fn.remove=function(){return this.each(function(){if(this.parentNode!=null){return this.parentNode.removeChild(this)}})}})(Quo)}).call(this);(function(){(function(E){var G,B,F,A,H,D,C;F=null;G=/WebKit\/([\d.]+)/;B={Android:/(Android)\s+([\d.]+)/,ipad:/(iPad).*OS\s([\d_]+)/,iphone:/(iPhone\sOS)\s([\d_]+)/,Blackberry:/(BlackBerry|BB10|Playbook).*Version\/([\d.]+)/,FirefoxOS:/(Mozilla).*Mobile[^\/]*\/([\d\.]*)/,webOS:/(webOS|hpwOS)[\s\/]([\d.]+)/};E.isMobile=function(){F=F||H();return F.isMobile&&F.os.name!=="firefoxOS"};E.environment=function(){F=F||H();return F};E.isOnline=function(){return navigator.onLine};H=function(){var J,I;I=navigator.userAgent;J={};J.browser=A(I);J.os=D(I);J.isMobile=!!J.os;J.screen=C();return J};A=function(J){var I;I=J.match(G);if(I){return I[0]}else{return J}};D=function(L){var J,I,K;J=null;for(I in B){K=L.match(B[I]);if(K){J={name:I==="iphone"||I==="ipad"?"ios":I,version:K[2].replace("_",".")};break}}return J};return C=function(){return{width:window.innerWidth,height:window.innerHeight}}})(Quo)}).call(this);(function(){(function(G){var K,C,I,A,L,E,D,J,F,H,B,M;K=1;A={};I={preventDefault:"isDefaultPrevented",stopImmediatePropagation:"isImmediatePropagationStopped",stopPropagation:"isPropagationStopped"};C={touchstart:"mousedown",touchmove:"mousemove",touchend:"mouseup",touch:"click",doubletap:"dblclick",orientationchange:"resize"};L=/complete|loaded|interactive/;G.fn.on=function(P,O,N){if(O==="undefined"||G.toType(O)==="function"){return this.bind(P,O)}else{return this.delegate(O,P,N)}};G.fn.off=function(P,O,N){if(O==="undefined"||G.toType(O)==="function"){return this.unbind(P,O)}else{return this.undelegate(O,P,N)}};G.fn.ready=function(N){if(L.test(document.readyState)){return N(G)}else{return G.fn.addEvent(document,"DOMContentLoaded",function(){return N(G)})}};G.Event=function(Q,P){var O,N;O=document.createEvent("Events");O.initEvent(Q,true,true,null,null,null,null,null,null,null,null,null,null,null,null);if(P){for(N in P){O[N]=P[N]}}return O};G.fn.bind=function(O,N){return this.each(function(){B(this,O,N)})};G.fn.unbind=function(O,N){return this.each(function(){M(this,O,N)})};G.fn.delegate=function(P,O,N){return this.each(function(R,Q){B(Q,O,N,P,function(S){return function(T){var V,U;U=G(T.target).closest(P,Q).get(0);if(U){V=G.extend(E(T),{currentTarget:U,liveFired:Q});return S.apply(U,[V].concat([].slice.call(arguments,1)))}}})})};G.fn.undelegate=function(P,O,N){return this.each(function(){M(this,O,N,P)})};G.fn.trigger=function(P,O,N){if(G.toType(P)==="string"){P=G.Event(P,O)}if(N!=null){P.originalEvent=N}return this.each(function(){this.dispatchEvent(P)})};G.fn.addEvent=function(P,O,N){if(P.addEventListener){return P.addEventListener(O,N,false)}else{if(P.attachEvent){return P.attachEvent("on"+O,N)}else{return P["on"+O]=N}}};G.fn.removeEvent=function(P,O,N){if(P.removeEventListener){return P.removeEventListener(O,N,false)}else{if(P.detachEvent){return P.detachEvent("on"+O,N)}else{return P["on"+O]=null}}};B=function(T,O,S,U,P){var Q,N,V,R;O=J(O);V=H(T);N=A[V]||(A[V]=[]);Q=P&&P(S,O);R={event:O,callback:S,selector:U,proxy:D(Q,S,T),delegate:Q,index:N.length};N.push(R);return G.fn.addEvent(T,R.event,R.proxy)};M=function(P,O,N,Q){var R;O=J(O);R=H(P);return F(R,O,N,Q).forEach(function(S){delete A[R][S.index];return G.fn.removeEvent(P,S.event,S.proxy)})};H=function(N){return N._id||(N._id=K++)};J=function(O){var N;N=G.isMobile()?O:C[O];return N||O};D=function(Q,P,O){var N;P=Q||P;N=function(S){var R;R=P.apply(O,[S].concat(S.data));if(R===false){S.preventDefault()}return R};return N};F=function(Q,P,O,N){return(A[Q]||[]).filter(function(R){return R&&(!P||R.event===P)&&(!O||R.callback===O)&&(!N||R.selector===N)})};return E=function(O){var N;N=G.extend({originalEvent:O},O);G.each(I,function(Q,P){N[Q]=function(){this[P]=function(){return true};return O[Q].apply(O,arguments)};return N[P]=function(){return false}});return N}})(Quo)}).call(this);(function(){(function($$){var CURRENT_TOUCH,EVENT,FIRST_TOUCH,GESTURE,GESTURES,HOLD_DELAY,TAPS,TOUCH_TIMEOUT,_angle,_capturePinch,_captureRotation,_cleanGesture,_distance,_fingersPosition,_getTouches,_hold,_isSwipe,_listenTouches,_onTouchEnd,_onTouchMove,_onTouchStart,_parentIfText,_swipeDirection,_trigger;TAPS=null;EVENT=void 0;GESTURE={};FIRST_TOUCH=[];CURRENT_TOUCH=[];TOUCH_TIMEOUT=void 0;HOLD_DELAY=650;GESTURES=["touch","tap","singleTap","doubleTap","hold","swipe","swiping","swipeLeft","swipeRight","swipeUp","swipeDown","rotate","rotating","rotateLeft","rotateRight","pinch","pinching","pinchIn","pinchOut","drag","dragLeft","dragRight","dragUp","dragDown"];GESTURES.forEach(function(e){$$.fn[e]=function(t){var n;n=e==="touch"?"touchend":e;return $$(document.body).delegate(this.selector,n,t)};return this});$$(document).ready(function(){return _listenTouches()});_listenTouches=function(){var e;e=$$(document.body);e.bind("touchstart",_onTouchStart);e.bind("touchmove",_onTouchMove);e.bind("touchend",_onTouchEnd);return e.bind("touchcancel",_cleanGesture)};_onTouchStart=function(e){var t,n,r,i;EVENT=e;r=Date.now();t=r-(GESTURE.last||r);TOUCH_TIMEOUT&&clearTimeout(TOUCH_TIMEOUT);i=_getTouches(e);n=i.length;FIRST_TOUCH=_fingersPosition(i,n);GESTURE.el=$$(_parentIfText(i[0].target));GESTURE.fingers=n;GESTURE.last=r;if(!GESTURE.taps){GESTURE.taps=0}GESTURE.taps++;if(n===1){if(n>=1){GESTURE.gap=t>0&&t<=250}return setTimeout(_hold,HOLD_DELAY)}else{if(n===2){GESTURE.initial_angle=parseInt(_angle(FIRST_TOUCH),10);GESTURE.initial_distance=parseInt(_distance(FIRST_TOUCH),10);GESTURE.angle_difference=0;return GESTURE.distance_difference=0}}};_onTouchMove=function(e){var t,n,r;EVENT=e;if(GESTURE.el){r=_getTouches(e);t=r.length;if(t===GESTURE.fingers){CURRENT_TOUCH=_fingersPosition(r,t);n=_isSwipe(e);if(n){GESTURE.prevSwipe=true}if(n||GESTURE.prevSwipe===true){_trigger("swiping")}if(t===2){_captureRotation();_capturePinch();e.preventDefault()}}else{_cleanGesture()}}return true};_isSwipe=function(e){var t,n,r;t=false;if(CURRENT_TOUCH[0]){n=Math.abs(FIRST_TOUCH[0].x-CURRENT_TOUCH[0].x)>30;r=Math.abs(FIRST_TOUCH[0].y-CURRENT_TOUCH[0].y)>30;t=GESTURE.el&&(n||r)}return t};_onTouchEnd=function(e){var t,n,r,i,u;EVENT=e;_trigger("touch");if(GESTURE.fingers===1){if(GESTURE.taps===2&&GESTURE.gap){_trigger("doubleTap");_cleanGesture()}else{if(_isSwipe()||GESTURE.prevSwipe){_trigger("swipe");u=_swipeDirection(FIRST_TOUCH[0].x,CURRENT_TOUCH[0].x,FIRST_TOUCH[0].y,CURRENT_TOUCH[0].y);_trigger("swipe"+u);_cleanGesture()}else{_trigger("tap");if(GESTURE.taps===1){TOUCH_TIMEOUT=setTimeout(function(){_trigger("singleTap");return _cleanGesture()},100)}}}}else{t=false;if(GESTURE.angle_difference!==0){_trigger("rotate",{angle:GESTURE.angle_difference});i=GESTURE.angle_difference>0?"rotateRight":"rotateLeft";_trigger(i,{angle:GESTURE.angle_difference});t=true}if(GESTURE.distance_difference!==0){_trigger("pinch",{angle:GESTURE.distance_difference});r=GESTURE.distance_difference>0?"pinchOut":"pinchIn";_trigger(r,{distance:GESTURE.distance_difference});t=true}if(!t&&CURRENT_TOUCH[0]){if(Math.abs(FIRST_TOUCH[0].x-CURRENT_TOUCH[0].x)>10||Math.abs(FIRST_TOUCH[0].y-CURRENT_TOUCH[0].y)>10){_trigger("drag");n=_swipeDirection(FIRST_TOUCH[0].x,CURRENT_TOUCH[0].x,FIRST_TOUCH[0].y,CURRENT_TOUCH[0].y);_trigger("drag"+n)}}_cleanGesture()}return EVENT=void 0};_fingersPosition=function(e,t){var n,r;r=[];n=0;e=e[0].targetTouches?e[0].targetTouches:e;while(n<t){r.push({x:e[n].pageX,y:e[n].pageY});n++}return r};_captureRotation=function(){var angle,diff,i,symbol;angle=parseInt(_angle(CURRENT_TOUCH),10);diff=parseInt(GESTURE.initial_angle-angle,10);if(Math.abs(diff)>20||GESTURE.angle_difference!==0){i=0;symbol=GESTURE.angle_difference<0?"-":"+";while(Math.abs(diff-GESTURE.angle_difference)>90&&i++<10){eval("diff "+symbol+"= 180;")}GESTURE.angle_difference=parseInt(diff,10);return _trigger("rotating",{angle:GESTURE.angle_difference})}};_capturePinch=function(){var e,t;t=parseInt(_distance(CURRENT_TOUCH),10);e=GESTURE.initial_distance-t;if(Math.abs(e)>10){GESTURE.distance_difference=e;return _trigger("pinching",{distance:e})}};_trigger=function(e,t){if(GESTURE.el){t=t||{};if(CURRENT_TOUCH[0]){t.iniTouch=GESTURE.fingers>1?FIRST_TOUCH:FIRST_TOUCH[0];t.currentTouch=GESTURE.fingers>1?CURRENT_TOUCH:CURRENT_TOUCH[0]}return GESTURE.el.trigger(e,t,EVENT)}};_cleanGesture=function(e){FIRST_TOUCH=[];CURRENT_TOUCH=[];GESTURE={};return clearTimeout(TOUCH_TIMEOUT)};_angle=function(e){var t,n,r;t=e[0];n=e[1];r=Math.atan((n.y-t.y)*-1/(n.x-t.x))*(180/Math.PI);if(r<0){return r+180}else{return r}};_distance=function(e){var t,n;t=e[0];n=e[1];return Math.sqrt((n.x-t.x)*(n.x-t.x)+(n.y-t.y)*(n.y-t.y))*-1};_getTouches=function(e){if($$.isMobile()){return e.touches}else{return[e]}};_parentIfText=function(e){if("tagName" in e){return e}else{return e.parentNode}};_swipeDirection=function(e,t,n,r){var i,u;i=Math.abs(e-t);u=Math.abs(n-r);if(i>=u){if(e-t>0){return"Left"}else{return"Right"}}else{if(n-r>0){return"Up"}else{return"Down"}}};return _hold=function(){if(GESTURE.last&&Date.now()-GESTURE.last>=HOLD_DELAY){_trigger("hold");return GESTURE.taps=0}}})(Quo)}).call(this);(function(){(function(A){A.fn.text=function(B){if(B||A.toType(B)==="number"){return this.each(function(){return this.textContent=B})}else{return this[0].textContent}};A.fn.html=function(C){var B;B=A.toType(C);if(C||B==="number"||B==="string"){return this.each(function(){var G,D,F,E;if(B==="string"||B==="number"){return this.innerHTML=C}else{this.innerHTML=null;if(B==="array"){E=[];for(D=0,F=C.length;D<F;D++){G=C[D];E.push(this.appendChild(G))}return E}else{return this.appendChild(C)}}})}else{return this[0].innerHTML}};A.fn.append=function(C){var B;B=A.toType(C);return this.each(function(){var D=this;if(B==="string"){return this.insertAdjacentHTML("beforeend",C)}else{if(B==="array"){return C.each(function(F,E){return D.appendChild(E)})}else{return this.appendChild(C)}}})};A.fn.prepend=function(C){var B;B=A.toType(C);return this.each(function(){var D=this;if(B==="string"){return this.insertAdjacentHTML("afterbegin",C)}else{if(B==="array"){return C.each(function(F,E){return D.insertBefore(E,D.firstChild)})}else{return this.insertBefore(C,this.firstChild)}}})};A.fn.replaceWith=function(C){var B;B=A.toType(C);this.each(function(){var D=this;if(this.parentNode){if(B==="string"){return this.insertAdjacentHTML("beforeBegin",C)}else{if(B==="array"){return C.each(function(F,E){return D.parentNode.insertBefore(E,D)})}else{return this.parentNode.insertBefore(C,this)}}}});return this.remove()};return A.fn.empty=function(){return this.each(function(){return this.innerHTML=null})}})(Quo)}).call(this);(function(){(function(G){var C,B,A,F,D,E;A="parentNode";C=/^\.([\w-]+)$/;B=/^#[\w\d-]+$/;F=/^[\w-]+$/;G.query=function(J,H){var I;H=H.trim();if(C.test(H)){I=J.getElementsByClassName(H.replace(".",""))}else{if(F.test(H)){I=J.getElementsByTagName(H)}else{if(B.test(H)&&J===document){I=J.getElementById(H.replace("#",""));if(!I){I=[]}}else{I=J.querySelectorAll(H)}}}if(I.nodeType){return[I]}else{return Array.prototype.slice.call(I)}};G.fn.find=function(I){var H;if(this.length===1){H=Quo.query(this[0],I)}else{H=this.map(function(){return Quo.query(this,I)})}return G(H)};G.fn.parent=function(I){var H;H=I?E(this):this.instance(A);return D(H,I)};G.fn.siblings=function(I){var H;H=this.map(function(K,J){return Array.prototype.slice.call(J.parentNode.children).filter(function(L){return L!==J})});return D(H,I)};G.fn.children=function(I){var H;H=this.map(function(){return Array.prototype.slice.call(this.children)});return D(H,I)};G.fn.get=function(H){if(H===undefined){return this}else{return this[H]}};G.fn.first=function(){return G(this[0])};G.fn.last=function(){return G(this[this.length-1])};G.fn.closest=function(J,I){var H,K;K=this[0];H=G(J);if(!H.length){K=null}while(K&&H.indexOf(K)<0){K=K!==I&&K!==document&&K.parentNode}return G(K)};G.fn.each=function(H){this.forEach(function(J,I){return H.call(J,I,J)});return this};E=function(I){var H;H=[];while(I.length>0){I=G.map(I,function(J){if((J=J.parentNode)&&J!==document&&H.indexOf(J)<0){H.push(J);return J}})}return H};return D=function(I,H){if(H===undefined){return G(I)}else{return G(I).filter(H)}}})(Quo)}).call(this);(function(){(function(D){var C,B,A;C=["-webkit-","-moz-","-ms-","-o-",""];D.fn.addClass=function(E){return this.each(function(){if(!A(E,this.className)){this.className+=" "+E;return this.className=this.className.trim()}})};D.fn.removeClass=function(E){return this.each(function(){if(!E){return this.className=""}else{if(A(E,this.className)){return this.className=this.className.replace(E," ").replace(/\s+/g," ").trim()}}})};D.fn.toggleClass=function(E){return this.each(function(){if(A(E,this.className)){return this.className=this.className.replace(E," ")}else{this.className+=" "+E;return this.className=this.className.trim()}})};D.fn.hasClass=function(E){return A(E,this[0].className)};D.fn.style=function(F,E){if(E){return this.each(function(){return this.style[F]=E})}else{return this[0].style[F]||B(this[0],F)}};D.fn.css=function(F,E){return this.style(F,E)};D.fn.vendor=function(J,F){var E,I,G,H;H=[];for(I=0,G=C.length;I<G;I++){E=C[I];H.push(this.style(""+E+J,F))}return H};A=function(G,F){var E;E=F.split(/\s+/g);return E.indexOf(G)>=0};return B=function(F,E){return document.defaultView.getComputedStyle(F,"")[E]}})(Quo)}).call(this);