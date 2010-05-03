  function t(){
                v=new Date();
                var bxx=document.getElementById('bxx');
                n=new Date();
                ss=pp;
                s=ss-Math.round((n.getTime()-v.getTime())/1000.);
                m=0;h=0;
                if(s<0){
				  //ps como session :P
                  bxx.innerHTML="Koniec<br>"+"<a href=?cp="+pl+">kontynuuj</a>"
                }else{
                  if(s>59){
                    m=Math.floor(s/60);
                    s=s-m*60
                  }
                  if(m>59){
                    h=Math.floor(m/60);
                    m=m-h*60
                  }
                  if(s<10){
                    s="0"+s
                  }
                  if(m<10){
                    m="0"+m
                  }
                  bxx.innerHTML=h+":"+m+":"+s+"<br><a href="+ps+"?unbau="+pk+"&cp="+pl+">Anuluj</a>"
                }
                pp=pp-1;
                window.setTimeout("t();",999);

              }