!function(e){function t(a){if(o[a])return o[a].exports;var n=o[a]={i:a,l:!1,exports:{}};return e[a].call(n.exports,n,n.exports,t),n.l=!0,n.exports}var o={};t.m=e,t.c=o,t.d=function(e,o,a){t.o(e,o)||Object.defineProperty(e,o,{configurable:!1,enumerable:!0,get:a})},t.n=function(e){var o=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(o,"a",o),o},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=34)}({34:function(e,t,o){e.exports=o(35)},35:function(e,t){$(document).ready(function(){$("input[data-key=email-config-status-btn]").on("change",function(){var e=$(this),t=e.prop("id"),o=e.data("change-url");$.ajax({type:"POST",url:o,data:{key:t,value:e.prop("checked")?1:0},success:function(e){e.error?Botble.showNotice("error",e.message):Botble.showNotice("success",e.message)},error:function(e){Botble.handleError(e)}})}),$(document).on("change","#email_driver",function(){"mailgun"===$(this).val()?($(".setting-mail-password").addClass("hidden"),$(".setting-mail-mail-gun").removeClass("hidden")):($(".setting-mail-password").removeClass("hidden"),$(".setting-mail-mail-gun").addClass("hidden"))}),$("#send-test-email-btn").on("click",function(e){e.preventDefault();var t=$(this),o=t.data("send-url");t.addClass("button-loading"),$.ajax({type:"POST",url:o,success:function(e){e.error?Botble.showNotice("error",e.message):Botble.showNotice("success",e.message),t.removeClass("button-loading")},error:function(e){Botble.handleError(e),t.removeClass("button-loading")}})}),"undefined"!=typeof CodeMirror&&Botble.initCodeEditor("mail-template-editor"),$(document).on("click",".btn-trigger-reset-to-default",function(e){e.preventDefault(),$("#reset-template-to-default-button").data("target",$(this).data("target")),$("#reset-template-to-default-modal").modal("show")}),$(document).on("click","#reset-template-to-default-button",function(e){e.preventDefault();var t=$(this);t.addClass("button-loading"),$.ajax({type:"POST",cache:!1,url:t.data("target"),data:{email_subject_key:$("input[name=email_subject_key]").val(),template_path:$("input[name=template_path]").val()},success:function(e){e.error?Botble.showNotice("error",e.message):(Botble.showNotice("success",e.message),setTimeout(function(){window.location.reload()},1e3)),t.removeClass("button-loading"),$("#reset-template-to-default-modal").modal("hide")},error:function(e){Botble.handleError(e),t.removeClass("button-loading")}})})})}});