document.addEventListener('DOMContentLoaded',function(){
  const root=document.querySelector('.hero-slideshow');
  if(!root) return;
  const autoplay=parseInt(root.getAttribute('data-autoplay')||'0',10);
  const slides=[...root.querySelectorAll('.hero-slide')];
  const dots=[...root.querySelectorAll('.slide-dot')];
  const indicators=[...root.querySelectorAll('.indicator-item')];
  const btnPrev=root.querySelector('.slide-nav.prev');
  const btnNext=root.querySelector('.slide-nav.next');
  if(slides.length<=1) return;
  let idx=slides.findIndex(s=>s.classList.contains('is-active'));
  if(idx<0) idx=0;
  let timer=null;
  function setActive(i){
    slides[idx].classList.remove('is-active');
    dots[idx]?.classList.remove('active');
    indicators[idx]?.classList.remove('active');
    idx=(i+slides.length)%slides.length;
    slides[idx].classList.add('is-active');
    dots[idx]?.classList.add('active');
    indicators[idx]?.classList.add('active');
  }
  function show(i){ if(i===idx) return; setActive(i); restart(); }
  function next(){ show(idx+1); }
  function prev(){ show(idx-1); }
  function start(){ if(autoplay>0){ stop(); timer=setTimeout(()=>{ next(); start(); },autoplay);} }
  function stop(){ if(timer){ clearTimeout(timer); timer=null; } }
  function restart(){ start(); }
  btnNext&&btnNext.addEventListener('click',()=>{ next(); restart(); });
  btnPrev&&btnPrev.addEventListener('click',()=>{ prev(); restart(); });
  dots.forEach((d,i)=>d.addEventListener('click',()=>show(i)));
  indicators.forEach((d,i)=>d.addEventListener('click',()=>show(i)));
  root.addEventListener('mouseenter',stop);
  root.addEventListener('mouseleave',start);
  start();
});
