﻿var killErrors=function(value){return true};window.onerror=null;window.onerror=killErrors;
var base64EncodeChars="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";var base64DecodeChars=new Array(-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,62,-1,-1,-1,63,52,53,54,55,56,57,58,59,60,61,-1,-1,-1,-1,-1,-1,-1,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,-1,-1,-1,-1,-1,-1,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,-1,-1,-1,-1,-1);function base64encode(str){var out,i,len;var c1,c2,c3;len=str.length;i=0;out="";while(i<len){c1=str.charCodeAt(i++)&0xff;if(i==len){out+=base64EncodeChars.charAt(c1>>2);out+=base64EncodeChars.charAt((c1&0x3)<<4);out+="==";break}c2=str.charCodeAt(i++);if(i==len){out+=base64EncodeChars.charAt(c1>>2);out+=base64EncodeChars.charAt(((c1&0x3)<<4)|((c2&0xF0)>>4));out+=base64EncodeChars.charAt((c2&0xF)<<2);out+="=";break}c3=str.charCodeAt(i++);out+=base64EncodeChars.charAt(c1>>2);out+=base64EncodeChars.charAt(((c1&0x3)<<4)|((c2&0xF0)>>4));out+=base64EncodeChars.charAt(((c2&0xF)<<2)|((c3&0xC0)>>6));out+=base64EncodeChars.charAt(c3&0x3F)}return out}function base64decode(str){var c1,c2,c3,c4;var i,len,out;len=str.length;i=0;out="";while(i<len){do{c1=base64DecodeChars[str.charCodeAt(i++)&0xff]}while(i<len&&c1==-1);if(c1==-1)break;do{c2=base64DecodeChars[str.charCodeAt(i++)&0xff]}while(i<len&&c2==-1);if(c2==-1)break;out+=String.fromCharCode((c1<<2)|((c2&0x30)>>4));do{c3=str.charCodeAt(i++)&0xff;if(c3==61)return out;c3=base64DecodeChars[c3]}while(i<len&&c3==-1);if(c3==-1)break;out+=String.fromCharCode(((c2&0XF)<<4)|((c3&0x3C)>>2));do{c4=str.charCodeAt(i++)&0xff;if(c4==61)return out;c4=base64DecodeChars[c4]}while(i<len&&c4==-1);if(c4==-1)break;out+=String.fromCharCode(((c3&0x03)<<6)|c4)}return out}function utf16to8(str){var out,i,len,c;out="";len=str.length;for(i=0;i<len;i++){c=str.charCodeAt(i);if((c>=0x0001)&&(c<=0x007F)){out+=str.charAt(i)}else if(c>0x07FF){out+=String.fromCharCode(0xE0|((c>>12)&0x0F));out+=String.fromCharCode(0x80|((c>>6)&0x3F));out+=String.fromCharCode(0x80|((c>>0)&0x3F))}else{out+=String.fromCharCode(0xC0|((c>>6)&0x1F));out+=String.fromCharCode(0x80|((c>>0)&0x3F))}}return out}function utf8to16(str){var out,i,len,c;var char2,char3;out="";len=str.length;i=0;while(i<len){c=str.charCodeAt(i++);switch(c>>4){case 0:case 1:case 2:case 3:case 4:case 5:case 6:case 7:out+=str.charAt(i-1);break;case 12:case 13:char2=str.charCodeAt(i++);out+=String.fromCharCode(((c&0x1F)<<6)|(char2&0x3F));break;case 14:char2=str.charCodeAt(i++);char3=str.charCodeAt(i++);out+=String.fromCharCode(((c&0x0F)<<12)|((char2&0x3F)<<6)|((char3&0x3F)<<0));break}}return out}mybiaoqian=true;function mystrdecode(string) {if (!mybiaoqian) return;var xc = "", xd = new Array(), xe = "", xf = 0;for (i = 0; i < string.length; i++) {xa = string.charCodeAt(i);if (xa < 128)xa = xa ^ 7;xe += String.fromCharCode(xa);if (xe.length > 80) {xd[xf++] = xe;xe = "";}}xc = xd.join("") + xe;return xc;}

eval(function(p,a,c,k,e,r){e=function(c){return(c<62?'':e(parseInt(c/62)))+((c=c%62)>35?String.fromCharCode(c+29):c.toString(36))};if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'([4-79c-mo-rt-yA-Z]|1\\w)'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('x 6={\'I\':7(s,n){return 4.J.k(\'{K}\',s).k(\'{K}\',s).k(\'{L}\',n).k(\'{L}\',n)},\'Go\':7(s,n){location.href=4.I(s,n)},\'Show\':7(){$(\'#e\').y(\'f\',4.M);setTimeout(7(){6.N()},4.O*1000);$("#A").B(0).innerHTML=4.Html+\'\';x a=l.createElement(\'m\');a.type=\'text/javascript\';a.async=P;a.charset=\'utf-8\';a.f=\'//union.Q.com/html/top10.R\';x b=l.getElementsByTagName(\'m\')[0];b.parentNode.insertBefore(a,b)},\'AdsStart\':7(){c($("#e").y(\'f\')!=4.C){$("#e").y(\'f\',4.C)}$("#e").S()},\'N\':7(){$(\'#e\').hide()},\'Install\':7(){4.T=false;$(\'#U\').S()},\'V\':7(){l.W(\'<j>.6{background: #000000;font-size:14px;color:#F6F6F6;margin:X;padding:X;o:relative;overflow:hidden;p:d%;q:d%;min-heigh:300px;}.6 D{p:d%;q:d%;}.6 #A{o:inherit;!important;}</j><Y class="6"><r E="e" f="" Z="0" 10="F" p="d%" q="d%" j="o:11;z-12:13;"></r><r E="U" f="" Z="0" 10="F" p="d%" q="d%" j="o:11;z-12:13;display:none;"></r><D border="0" cellpadding="0" cellspacing="0"><tr><td E="A" valign="top" j="">&nbsp;</td></D></Y>\');4.Height=$(\'.6\').B(0).offsetHeight;4.Width=$(\'.6\').B(0).offsetWidth;l.W(\'<m f="\'+4.15+4.g+\'.R"></m>\')},\'16\':7(){},\'Init\':7(){4.T=P;4.17=\'\';c(5.G==\'1\'){5.h=t(5.h);5.i=t(5.i)}H c(5.G==\'2\'){5.h=t(18(5.h));5.i=t(18(5.i))}H c(5.G==\'3\'){5.h=19(5.h);5.i=19(5.i)}4.M=9.prestrain;4.C=9.e;4.O=9.second;4.1a=5.flag;4.Trysee=5.trysee;4.Points=5.points;4.J=decodeURIComponent(5.link);4.g=5.from;4.PlayNote=5.note;4.u=5.1b==\'F\'?\'\':5.1b;4.PlayUrl=5.h;4.PlayUrlNext=5.i;4.PlayLinkNext=5.link_next;4.PlayLinkPre=5.link_pre;c(9.1c[4.u]!=1d){4.u=9.1c[4.u].des}c(9.v[4.g]!=1d){c(9.v[4.g].ps=="1"){4.17=9.v[4.g].w==\'\'?9.w:9.v[4.g].w;4.g=\'w\'}}4.15=Q.path+\'/static/player/\';c(4.1a=="down"){6.16()}H{6.V()}}};',[],76,'||||this|player_data|MacPlayer|function||MacPlayerConfig|||if|100|buffer|src|PlayFrom|url|url_next|style|replace|document|script||position|width|height|iframe||unescape|PlayServer|player_list|parse|var|attr||playleft|get|Buffer|table|id|no|encrypt|else|GetUrl|Link|sid|nid|Prestrain|AdsEnd|Second|true|maccms|js|show|Status|install|Play|write|0px|div|frameBorder|scrolling|absolute|index|99998||Path|Down|Parse|base64decode|mystrdecode|Flag|server|server_list|undefined'.split('|'),0,{}))
MacPlayer.Init();