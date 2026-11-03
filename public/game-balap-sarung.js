// ===== BALAP RIDER - Kamen Rider W =====
(function(){
const VW=1920,VH=1080;
const cv=document.getElementById('game'),ctx=cv.getContext('2d'),stage=document.getElementById('stage');
let DPR=Math.min(window.devicePixelRatio||1,2);
function setupCanvas(){cv.width=VW*DPR;cv.height=VH*DPR;cv.style.width=VW+'px';cv.style.height=VH+'px';ctx.setTransform(DPR,0,0,DPR,0,0);}
function fit(){const s=Math.min(window.innerWidth/VW,window.innerHeight/VH);stage.style.transform='translate(-50%,-50%) scale('+s+')';document.getElementById('rotate').style.display=(window.innerHeight>window.innerWidth)?'flex':'none';}
window.addEventListener('resize',()=>{DPR=Math.min(window.devicePixelRatio||1,2);setupCanvas();fit();});
setupCanvas();fit();
const W=VW,H=VH,GRAV=1.9,GROUND=()=>H-150;
let state='menu',score=0,coins=0,speed=15,frame=0,spawnT=0,coinT=0,stepT=0;
const player={x:230,y:0,vy:0,w:130,h:180,onGround:true,down:false,run:0};
player.y=GROUND()-player.h;
let obstacles=[],coinItems=[],particles=[];
const OB_TYPES=['polisi','truk','orang'];
function reset(){score=0;coins=0;speed=15;frame=0;spawnT=0;coinT=0;stepT=0;obstacles=[];coinItems=[];particles=[];player.y=GROUND()-player.h;player.vy=0;player.onGround=true;player.down=false;}
function jump(){if(state!=='play')return;if(player.onGround){player.vy=-38;player.onGround=false;sfxJump();}}
function setDown(v){player.down=v;}
window.addEventListener('keydown',e=>{if(e.code==='Space'||e.code==='ArrowUp'){e.preventDefault();jump();}if(e.code==='ArrowDown')setDown(true);});
window.addEventListener('keyup',e=>{if(e.code==='ArrowDown')setDown(false);});
cv.addEventListener('touchstart',e=>{e.preventDefault();jump();},{passive:false});
cv.addEventListener('mousedown',()=>jump());
function rnd(a,b){return Math.random()*(b-a)+a;}
function spawnObstacle(){const t=OB_TYPES[Math.floor(Math.random()*OB_TYPES.length)];let h,w;if(t==='truk'){h=210;w=250;}else if(t==='polisi'){h=165;w=110;}else{h=135;w=105;}obstacles.push({t,x:W+60,y:GROUND()-h,w,h});}
function spawnCoin(){coinItems.push({x:W+40,y:GROUND()-rnd(70,340),r:34,got:false,spin:0});}
function burst(x,y,color){for(let i=0;i<14;i++){particles.push({x,y,vx:rnd(-7,7),vy:rnd(-12,-3),life:34,color});}}
function update(){
 frame++;speed=15+Math.floor(score/300);if(speed>42)speed=42;
 if(!player.onGround){player.vy+=GRAV;player.y+=player.vy;if(player.y>=GROUND()-player.h){player.y=GROUND()-player.h;player.vy=0;player.onGround=true;}}
 player.run+=player.onGround?speed*0.35:0;
 if(player.onGround){stepT--;if(stepT<=0){sfxStep();stepT=Math.max(6,Math.round(26-speed*0.3));}}
 const pw=player.down?player.h:player.w,ph=player.down?player.w:player.h;
 const py=player.down?player.y+player.w-player.h:player.y;
 if(frame%2===0)score++;
 spawnT--;if(spawnT<=0){spawnObstacle();spawnT=Math.max(55,150-Math.floor(score/45))+Math.random()*50;}
 coinT--;if(coinT<=0){spawnCoin();coinT=rnd(55,130);}
 for(let i=obstacles.length-1;i>=0;i--){const o=obstacles[i];o.x-=speed;if(o.x+o.w<-30){obstacles.splice(i,1);continue;}if(collide(player.x,py,pw,ph,o.x,o.y,o.w,o.h)){gameOver();}}
 for(let i=coinItems.length-1;i>=0;i--){const c=coinItems[i];c.x-=speed;c.spin+=0.25;if(c.x+c.r<-30){coinItems.splice(i,1);continue;}if(!c.got&&collide(player.x,py,pw,ph,c.x-c.r,c.y-c.r,c.r*2,c.r*2)){c.got=true;coins++;sfxCoin();burst(c.x,c.y,'#f59e0b');coinItems.splice(i,1);}}
 for(let i=particles.length-1;i>=0;i--){const p=particles[i];p.x+=p.vx;p.y+=p.vy;p.vy+=0.5;p.life--;if(p.life<=0)particles.splice(i,1);}
}
function collide(ax,ay,aw,ah,bx,by,bw,bh){return ax<bx+bw&&ax+aw>bx&&ay<by+bh&&ay+ah>by;}
function gameOver(){state='over';sfxCrash();document.getElementById('over').style.display='block';document.getElementById('finalScore').textContent=score;document.getElementById('finalCoin').textContent=coins;}
// ===== AUDIO (Web Audio API: BGM sintetis + SFX) =====
let actx=null,masterGain=null,musicGain=null,bgmOn=false,bgmTimer=null,bgmStep=0,bgmMp3=null,mp3Ready=false;
// MP3 BGM opsional: cari file di /audio/ (mp3>m4a>webm>ogg). Kalau ADA -> dipakai (looping). Kalau TIDAK -> fallback ke BGM sintetis.
(function(){const cands=['/audio/bgm.mp3','/audio/bgm.m4a','/audio/bgm.webm','/audio/bgm.ogg'];let i=0;
 function tryNext(){if(i>=cands.length){mp3Ready=false;bgmMp3=null;return;}
  const a=new Audio(cands[i]);a.loop=true;a.volume=0.55;a.preload='auto';
  const ok=()=>{mp3Ready=true;bgmMp3=a;};
  a.addEventListener('canplaythrough',ok);a.addEventListener('loadeddata',ok);
  a.addEventListener('error',()=>{i++;tryNext();});
  a.load();}
 tryNext();})();
function initAudio(){
 if(actx)return;
 try{actx=new (window.AudioContext||window.webkitAudioContext)();}catch(e){return;}
 masterGain=actx.createGain();masterGain.gain.value=0.9;masterGain.connect(actx.destination);
 musicGain=actx.createGain();musicGain.gain.value=0.0;musicGain.connect(masterGain);
}
function tone(freq,dur,type,vol,when,dest,slideTo){
 if(!actx)return;const t=actx.currentTime+(when||0);
 const o=actx.createOscillator(),g=actx.createGain();
 o.type=type||'square';o.frequency.setValueAtTime(freq,t);
 if(slideTo)o.frequency.exponentialRampToValueAtTime(slideTo,t+dur);
 g.gain.setValueAtTime(0.0001,t);
 g.gain.exponentialRampToValueAtTime(vol||0.2,t+0.012);
 g.gain.exponentialRampToValueAtTime(0.0001,t+dur);
 o.connect(g);g.connect(dest||masterGain);o.start(t);o.stop(t+dur+0.03);
}
function noise(dur,vol,when,dest){
 if(!actx)return;const t=actx.currentTime+(when||0);
 const n=Math.floor(actx.sampleRate*dur),buf=actx.createBuffer(1,n,actx.sampleRate),d=buf.getChannelData(0);
 for(let i=0;i<n;i++)d[i]=(Math.random()*2-1)*(1-i/n);
 const s=actx.createBufferSource();s.buffer=buf;const g=actx.createGain();g.gain.value=vol||0.2;
 s.connect(g);g.connect(dest||masterGain);s.start(t);
}
function sfxJump(){initAudio();if(!actx)return;tone(300,0.18,'square',0.22,0,null,760);}
function sfxStep(){if(!actx||!bgmOn)return;noise(0.05,0.05);}
function sfxCoin(){initAudio();if(!actx)return;tone(880,0.08,'square',0.2);tone(1320,0.12,'square',0.18,0.07);}
function sfxCrash(){initAudio();if(!actx)return;tone(220,0.5,'sawtooth',0.25,0,null,60);noise(0.35,0.22,0.02);}
// BGM tema epic bergaya rider (A-minor). Bass + lead + drum, 16 step.
const BASS=[110,0,110,0,146.83,0,146.83,0,98,0,98,0,130.81,0,164.81,0];
const LEAD=[440,523.25,659.25,523.25,587.33,493.88,440,0,392,493.88,587.33,493.88,523.25,440,392,0];
function bgmTick(){
 if(!actx)return;
 const s=bgmStep%16;
 if(BASS[s])tone(BASS[s],0.22,'triangle',0.5,0,musicGain);
 if(LEAD[s])tone(LEAD[s],0.16,'square',0.14,0,musicGain);
 if(s%4===0)tone(70,0.12,'sine',0.6,0,musicGain);
 if(s===4||s===12)noise(0.08,0.12,0,musicGain);
 if(s%2===1)noise(0.03,0.05,0,musicGain);
 bgmStep++;
}
function startSynth(){if(!actx||bgmTimer)return;if(actx.state==='suspended')actx.resume();
 musicGain.gain.cancelScheduledValues(actx.currentTime);musicGain.gain.setValueAtTime(0.0001,actx.currentTime);musicGain.gain.exponentialRampToValueAtTime(0.5,actx.currentTime+0.3);
 bgmTick();bgmTimer=setInterval(bgmTick,150);}
function startBgm(){initAudio();if(actx&&actx.state==='suspended')actx.resume();
 if(bgmMp3&&(mp3Ready||bgmMp3.readyState>=2)){try{bgmMp3.currentTime=0;const p=bgmMp3.play();if(p&&p.catch)p.catch(()=>startSynth());return;}catch(e){startSynth();return;}}
 startSynth();}
function stopBgm(){try{if(bgmMp3&&!bgmMp3.paused){bgmMp3.pause();bgmMp3.currentTime=0;}}catch(e){}
 if(bgmTimer){clearInterval(bgmTimer);bgmTimer=null;}
 if(actx&&musicGain){musicGain.gain.cancelScheduledValues(actx.currentTime);musicGain.gain.setValueAtTime(musicGain.gain.value,actx.currentTime);musicGain.gain.exponentialRampToValueAtTime(0.0001,actx.currentTime+0.25);}}
function toggleMusic(){bgmOn=!bgmOn;const b=document.getElementById('musicBtn');
 if(bgmOn){startBgm();}else{stopBgm();}
 if(b){b.textContent=bgmOn?'â™ª MUSIK ON':'â™ª MUSIK OFF';b.classList.toggle('on',bgmOn);}}
// ===== GAMBAR: LATAR TEMPAT TERBUKA (siang) =====
function rrect(x,y,w,h,r){ctx.beginPath();ctx.moveTo(x+r,y);ctx.arcTo(x+w,y,x+w,y+h,r);ctx.arcTo(x+w,y+h,x,y+h,r);ctx.arcTo(x,y+h,x,y,r);ctx.arcTo(x,y,x+w,y,r);ctx.closePath();}
function rnd0(i,a,b){return a+((i*37)%(b-a));}
function drawBg(){
 const gy=GROUND(),hz=gy-40;
 let g=ctx.createLinearGradient(0,0,0,gy);
 g.addColorStop(0,'#2f8fe0');g.addColorStop(.5,'#79c4f5');g.addColorStop(1,'#dff2ff');
 ctx.fillStyle=g;ctx.fillRect(0,0,W,gy);
 const sx=1560,sy=200;let sg=ctx.createRadialGradient(sx,sy,20,sx,sy,220);
 sg.addColorStop(0,'rgba(255,247,180,.95)');sg.addColorStop(.35,'rgba(255,236,140,.45)');sg.addColorStop(1,'rgba(255,236,140,0)');
 ctx.fillStyle=sg;ctx.beginPath();ctx.arc(sx,sy,220,0,7);ctx.fill();
 ctx.fillStyle='#fff6b0';ctx.beginPath();ctx.arc(sx,sy,66,0,7);ctx.fill();
 ctx.fillStyle='rgba(255,255,255,.92)';
 for(let i=0;i<6;i++){const bx=((i*420-frame*speed*0.12)%(W+400))-200,by=90+((i*97)%210),s=0.8+((i*13)%50)/100;
  ctx.beginPath();ctx.arc(bx,by,36*s,0,7);ctx.arc(bx+36*s,by+10*s,26*s,0,7);ctx.arc(bx-34*s,by+12*s,24*s,0,7);ctx.arc(bx+8*s,by-20*s,28*s,0,7);ctx.fill();}
 for(let l=0;l<2;l++){const off=l*60,par=0.15+l*0.1,col=l?'#7f97b0':'#9db3c9';
  ctx.fillStyle=col;
  for(let i=0;i<8;i++){const bx=((i*300-frame*speed*par)%(W+420))-210,bh=170+l*40+((i*57)%90);
   ctx.beginPath();ctx.moveTo(bx,hz+off);ctx.lineTo(bx+150,hz+off-bh);ctx.lineTo(bx+300,hz+off);ctx.closePath();ctx.fill();}}
 ctx.fillStyle='#5bbf4a';
 for(let i=0;i<9;i++){const bx=((i*260-frame*speed*0.3)%(W+340))-170;ctx.beginPath();ctx.ellipse(bx+130,hz+20,200,80,0,Math.PI,0);ctx.fill();}
 ctx.fillStyle='#5cbf46';ctx.fillRect(0,hz,W,gy-hz+2);
 ctx.fillStyle='#4fad3d';ctx.fillRect(0,hz+30,W,gy-hz);
 ctx.fillStyle='#c79a5b';ctx.fillRect(0,gy,W,H-gy);
 ctx.fillStyle='#b5854a';ctx.fillRect(0,gy,W,8);
 ctx.fillStyle='rgba(160,120,70,.5)';
 for(let i=0;i<22;i++){const rx=((i*130-frame*speed)%(W+130))-65;ctx.beginPath();ctx.ellipse(rx,gy+rnd0(i,60,110),30,9,0,0,7);ctx.fill();}
 ctx.fillStyle='#3f9e32';ctx.fillRect(0,H-16,W,16);
 for(let i=0;i<6;i++){const bx=((i*360-frame*speed*0.5)%(W+380))-190;
  ctx.fillStyle='#6b4a2a';ctx.fillRect(bx,gy-56,16,58);
  ctx.fillStyle='#2f9e3f';ctx.beginPath();ctx.arc(bx+8,gy-70,34,0,7);ctx.arc(bx+26,gy-54,24,0,7);ctx.arc(bx-12,gy-54,24,0,7);ctx.fill();
  ctx.fillStyle='#43b84e';ctx.beginPath();ctx.arc(bx+2,gy-78,16,0,7);ctx.fill();}
 for(let i=0;i<8;i++){const bx=((i*230-frame*speed*0.8)%(W+240))-120;ctx.fillStyle='#3fae3f';ctx.beginPath();ctx.arc(bx,gy-4,18,Math.PI,0);ctx.arc(bx+26,gy-2,14,Math.PI,0);ctx.fill();}
 ctx.strokeStyle='rgba(30,40,50,.7)';ctx.lineWidth=3;
 for(let i=0;i<4;i++){const bx=((i*520-frame*speed*0.4)%(W+400))-200,by=130+((i*83)%140),f=Math.sin((frame*0.15)+i)*6;
  ctx.beginPath();ctx.moveTo(bx-14,by+f);ctx.lineTo(bx,by-4);ctx.lineTo(bx+14,by+f);ctx.stroke();}
}
function drawPlayer(){
 const bob=player.onGround?Math.sin(player.run*0.6)*3:0;
 const x=player.x,y=player.y,w=player.w,h=player.h;
 ctx.save();
 ctx.translate(x+w/2,y+h+bob);
 if(player.down){ctx.rotate(-Math.PI/2);ctx.translate(-h/2,0);}
 const P=4;
 function px(a,b,cw,ch,col){ctx.fillStyle=col;ctx.fillRect(a*P,b*P,cw*P,ch*P);}
 const C={k:'#181820',d:'#2a2a34',s:'#c7ccd6',g:'#3fbf4a',G:'#2a8a32',r:'#ff2e3f',p:'#8b3ff0',w:'#ffffff'};
 const sw=player.onGround?Math.sin(player.run*0.6)*2:0;
 px(-6+sw,-14,5,14,C.G);px(1-sw,-14,5,14,C.G);
 px(-6+sw,-3,5,3,C.k);px(1-sw,-3,5,3,C.k);
 px(-9,-30,18,16,C.k);
 px(-7,-28,5,4,C.p);px(2,-28,5,4,C.p);
 px(-9,-26,18,2,C.d);
 px(-14,-29,5,11,C.g);px(9,-29,5,11,C.g);
 px(-14,-19,5,3,C.G);px(9,-19,5,3,C.G);
 px(-8,-40,16,11,C.k);
 px(-8,-41,16,2,C.d);
 px(-6,-44,2,4,C.s);px(4,-44,2,4,C.s);
 px(-6,-45,2,1,C.w);px(4,-45,2,1,C.w);
 px(-6,-38,12,6,C.d);
 px(-5,-37,3,3,C.r);px(2,-37,3,3,C.r);
 px(-5,-37,3,1,C.w);px(2,-37,3,1,C.w);
 px(-3,-34,6,2,C.k);
 px(-8,-40,2,4,C.g);px(6,-40,2,4,C.g);
 ctx.fillStyle=C.s;ctx.beginPath();ctx.moveTo(14*P,-24*P);ctx.lineTo(9*P,-46*P);ctx.lineTo(11*P,-46*P);ctx.lineTo(17*P,-24*P);ctx.closePath();ctx.fill();
 ctx.fillStyle=C.w;ctx.fillRect(15*P,-30*P,2*P,5*P);
 ctx.fillStyle='#5a5a66';ctx.fillRect(12*P,-24*P,3*P,10*P);
 ctx.restore();
}
function drawObstacles(){
 obstacles.forEach(o=>{ctx.save();ctx.translate(o.x,o.y);
  if(o.t==='truk'){
   ctx.fillStyle='#2d5016';rrect(0,20,o.w,o.h-20,12);ctx.fill();
   ctx.fillStyle='#3a6a1e';rrect(o.w-70,20,66,50,8);ctx.fill();
   ctx.fillStyle='#b71c1c';rrect(12,44,o.w-96,66,8);ctx.fill();
   ctx.fillStyle='#fff';ctx.font='bold 30px Arial';ctx.textAlign='left';ctx.textBaseline='middle';ctx.fillText('TRUK',30,78);
   ctx.fillStyle='#1a1a1a';ctx.beginPath();ctx.arc(48,o.h-16,24,0,7);ctx.arc(o.w-48,o.h-16,24,0,7);ctx.fill();
   ctx.fillStyle='#5a5a66';ctx.beginPath();ctx.arc(48,o.h-16,10,0,7);ctx.arc(o.w-48,o.h-16,10,0,7);ctx.fill();}
  else if(o.t==='polisi'){
   ctx.fillStyle='#1e3a8a';rrect(6,30,o.w-12,o.h-46,8);ctx.fill();
   ctx.fillStyle='#f5c99b';ctx.beginPath();ctx.arc(o.w/2,22,22,0,7);ctx.fill();
   ctx.fillStyle='#0b1e4a';ctx.beginPath();ctx.arc(o.w/2,16,23,Math.PI,0);ctx.fill();
   ctx.fillStyle='#fff';ctx.fillRect(12,44,o.w-24,12);
   ctx.fillStyle='#111';ctx.font='bold 13px Arial';ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillText('POLISI',o.w/2,50);
   ctx.fillStyle='#f59e0b';ctx.fillRect(6,o.h-70,o.w-12,8);
   ctx.fillStyle='#0f172a';ctx.fillRect(16,o.h-34,20,34);ctx.fillRect(o.w-36,o.h-34,20,34);}
  else{
   ctx.fillStyle='#334155';rrect(10,32,o.w-20,o.h-56,8);ctx.fill();
   ctx.fillStyle='#f5c99b';ctx.beginPath();ctx.arc(o.w/2,24,18,0,7);ctx.fill();
   ctx.fillStyle='#5a3a22';ctx.beginPath();ctx.arc(o.w/2,20,18,Math.PI,0);ctx.fill();
   ctx.fillStyle='#111';ctx.fillRect(16,o.h-32,18,32);ctx.fillRect(o.w-34,o.h-32,18,32);}
  ctx.restore();});
}
function drawCoins(){
 coinItems.forEach(c=>{const sx=Math.abs(Math.cos(c.spin))*c.r+6;
  ctx.save();ctx.translate(c.x,c.y);
  ctx.fillStyle='#f59e0b';ctx.beginPath();ctx.ellipse(0,0,sx,c.r,0,0,7);ctx.fill();
  ctx.fillStyle='#b45309';ctx.beginPath();ctx.ellipse(0,0,sx*0.55,c.r*0.55,0,0,7);ctx.fill();
  ctx.fillStyle='#fff';ctx.font='bold 22px Arial';ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillText('S',0,1);
  ctx.restore();});
}
function drawParticles(){particles.forEach(p=>{ctx.globalAlpha=p.life/34;ctx.fillStyle=p.color;ctx.fillRect(p.x,p.y,9,9);ctx.globalAlpha=1;});}
function loop(){
 if(state==='play')update();
 drawBg();drawObstacles();drawCoins();drawPlayer();drawParticles();
 document.getElementById('score').textContent=score;
 document.getElementById('coin').textContent=coins;
 requestAnimationFrame(loop);
}
loop();
function startGame(){initAudio();if(actx&&actx.state==='suspended')actx.resume();reset();state='play';document.getElementById('menu').style.display='none';document.getElementById('over').style.display='none';}
document.getElementById('startBtn').addEventListener('click',startGame);
document.getElementById('againBtn').addEventListener('click',startGame);
document.getElementById('musicBtn').addEventListener('click',e=>{e.stopPropagation();toggleMusic();});
})();
