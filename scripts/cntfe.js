  function tfe(){
                v=new Date();
                var bxxfe=document.getElementById('bxxfe');
                n=new Date();
                ssfe=ppfe;
                ssse=ssfe-Math.round((n.getTime()-v.getTime())/1000.);
                me=0;he=0;
                if(ssse<0){
				  //ps como session :P
                  bxxfe.innerHTML="-"
                }else{
                  if(ssse>59){
                    me=Math.floor(ssse/60);
                    ssse=ssse-me*60
                  }
                  if(me>59){
                    he=Math.floor(me/60);
                    me=me-he*60
                  }
                  if(ssse<10){
                    ssse="0"+ssse
                  }
                  if(me<10){
                    me="0"+me
                  }
                  bxxfe.innerHTML=he+":"+me+":"+ssse
                }
                ppfe=ppfe-1;
                window.setTimeout("tfe();",999);

              }