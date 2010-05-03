  function tfs(){
                v=new Date();
                var bxxfs=document.getElementById('bxxfs');
                n=new Date();
                ssfs=ppfs;
                sss=ssfs-Math.round((n.getTime()-v.getTime())/1000.);
                ms=0;hs=0;
                if(sss<0){
				  //ps como session :P
                  bxxfs.innerHTML="-"
                }else{
                  if(sss>59){
                    ms=Math.floor(sss/60);
                    sss=sss-ms*60
                  }
                  if(ms>59){
                    hs=Math.floor(ms/60);
                    ms=ms-hs*60
                  }
                  if(sss<10){
                    sss="0"+sss
                  }
                  if(ms<10){
                    ms="0"+ms
                  }
                  bxxfs.innerHTML=hs+":"+ms+":"+sss
                }
                ppfs=ppfs-1;
                window.setTimeout("tfs();",999);

              }