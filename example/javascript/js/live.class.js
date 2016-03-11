define(["klass","router"], function(klass,router) {
	var pages = [];
	var page = new klass({
		Target:null,
		Level:0,
		Arguments:{},
		Name:"",
		init: function init(args) {
			this.Arguments = args; 
			this.abstract  = false;
			var path = router.urlPath().substr(1);
			path = path.replace("/", "_");
			if (path == "") path = "home";
			this.Name = path;
			this.CreatePage();
			this.ResumePage();
		},
		DeadCircle:function(){
			var level = this.Level;
			if(level == 0){
				this.Pause();
			}else if(level == 1){
				this.Stop();
			}else{
				this.destroy();
			}
			this.StopPage();
		},
		ResumePage:function(){
			
		},
		DestroyPage:function(){
			for(var index=0;index<pages.length;index++){
				if(pages[index].Name == this.Name){
					pages.splice(index,1);
				}
			}
		},
		CreatePage:function(){
			if(!this.HasPage())pages.push(this);
		},
		StopPage:function(){
			for(var index= pages.length-1;index>=0;index--){
				if(pages[index].Name != this.Name){
					if(pages[index].Page && pages[index].Page.live == "pause"){
						pages[index].Page.stop();
					}
				}
			}
		},
		HasPage:function(){
			for(var index= pages.length-1;index>=0;index--){
				if(pages[index].Name == this.Name) return true;
			}
			return false;
		},
		LiveCircle:function(){
			if(this.Target){
				var live = this.Target.live;
				if(live == "pause"){
					this.Active();
				}else if(live == "stop"){
					this.Active();
				}else{
					this.Create();
					this.Active();
				}
			}else{
				this.Create();
				this.Active();
			}
			this.Resume();
		},
		/*第一生命周期*/
		Create:function Create(){
			if(this.Target){
				this.Target.create();
			}
		},
		/*第二生命周期*/
		Active:function Active(){
			if(this.Target){
				this.Target.active();
			}
		},
		/*第三生命周期*/
		Resume:function Resume(){
			if(this.Target){
				this.Target.resume();
			}
		},
		/*第四生命周期*/
		Pause:function Pause(){
			if(this.Target){
				this.Target.pause();
			}
		},
		/*第五生命周期*/
		Stop:function Stop(){
			if(this.Target){
				this.Target.stop();
			}
		}
		/*第六生命周期*/
		,destroy: function destroy(args) {
			if(this.Target){
				this.Target.destroy();
			}
			this.DestroyPage();
			this.Target = null;
		}
	})
	return page;
})