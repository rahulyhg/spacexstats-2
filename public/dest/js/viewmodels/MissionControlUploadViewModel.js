define(["jquery","knockout","ko.mapping"],function(e,t,i){var n=function(){function n(e){var i=this;i.id=t.observable(e.object_id),i.type=t.observable(e.type),i.subtype=t.observable(),i.filename=t.observable(e.filename),i.originalName=t.observable(e.original_name),i.thumbnail=t.computed(function(){return"/media/small/"+i.filename()})}t.components.register("upload",{require:"components/upload/upload"}),t.components.register("tweet",{require:"components/tweet/tweet"}),t.components.register("rich-select",{require:"components/rich-select/rich-select"});var o=this,a={create:function(e){return new n(e.data)}};o.visibleSection=t.observable("upload"),o.changeVisibleSection=function(e){o.visibleSection(e)},o.uploadSection=t.observable("dropzone"),o.rawFiles=t.observableArray([]),o.rawFiles.subscribe(function(e){i.fromJS(e,a,o.uploadedFiles),o.uploadSection(o.rawFiles().length>0?"form":"dropzone")}),o.uploadedFiles=t.observableArray(),o.templateObjectType=function(e){return 1==e.type?"image-file-template":2==e.type?"gif-file-template":3==e.type?"audio-file-template":4==e.type?"video-file-template":5==e.type?"document-file-template":void 0},o.changeVisibleTemplate=function(e,i){var n=t.contextFor(i.target);o.visibleTemplate(t.unwrap(n.$index))},o.visibleTemplate=t.observable(0),o.formButtonText=t.computed(function(){return"Submit"}),o.postType=t.observable(),o.submitToMissionControl=function(t,i){console.log(t),e.ajax("/missioncontrol/create/submit",{dataType:"json",type:"POST",headers:i,data:{files:t},success:function(e){console.log(e)}})},o.submitFiles=function(t,i){var n=e(i.currentTarget).siblings(".files-details").find("form").map(function(){return{title:e(this).find('[name="title"]').val(),summary:e(this).find('[name="summary"]').val(),mission_id:e(this).find("#mission_id").data("value"),author:e(this).find('[name="author"]').val(),attribution:e(this).find('[name="attribution"]').val(),tags:e(this).find(".tagger").prop("value"),type:e(this).find('[name="type"]').val(),association:e(this).find('[name="anonymous"]').val()}}).get(),a={"Submission-Type":"files"};o.submitToMissionControl(n,a)},o.submitPost=function(t,i){var n=e(i.currentTarget).parent();if("tweet"==o.postType())var a={url:n.find('[name="tweet-url"]').val(),author:n.find('[name="tweet-author"]').val(),tweet:n.find('[name="tweet"]').val()},s={"Submission-Type":"tweet"};o.submitToMissionControl(a,s)},o.submitWriting=function(e,t){}};return n});