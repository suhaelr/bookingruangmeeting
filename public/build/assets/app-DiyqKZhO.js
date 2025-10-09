document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll(".bg-red-100, .bg-green-100, .bg-blue-100").forEach(e=>{setTimeout(()=>{e.style.transition="opacity 0.5s",e.style.opacity="0",setTimeout(()=>e.remove(),500)},5e3)});const a=document.querySelector('input[type="text"]');a&&a.addEventListener("input",function(e){const s=e.target.value.toLowerCase();document.querySelectorAll("tbody tr").forEach(o=>{o.textContent.toLowerCase().includes(s)?o.style.display="":o.style.display="none"})}),window.togglePassword=function(){const e=document.getElementById("password"),s=document.getElementById("password-icon");e.type==="password"?(e.type="text",s.classList.remove("fa-eye"),s.classList.add("fa-eye-slash")):(e.type="password",s.classList.remove("fa-eye-slash"),s.classList.add("fa-eye"))};const t=document.querySelector(".max-w-7xl");t&&t.classList.add("fade-in"),document.querySelectorAll("tbody tr").forEach(e=>{e.addEventListener("mouseenter",function(){this.style.transform="translateX(5px)"}),e.addEventListener("mouseleave",function(){this.style.transform="translateX(0)"})}),document.querySelectorAll("button").forEach(e=>{e.addEventListener("click",function(s){const n=document.createElement("span"),o=this.getBoundingClientRect(),i=Math.max(o.width,o.height),r=s.clientX-o.left-i/2,d=s.clientY-o.top-i/2;n.style.width=n.style.height=i+"px",n.style.left=r+"px",n.style.top=d+"px",n.classList.add("ripple"),this.appendChild(n),setTimeout(()=>{n.remove()},600)})});const l=document.createElement("style");l.textContent=`
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `,document.head.appendChild(l)});function u(c,a="success"){const t=document.createElement("div");t.className=`fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${a==="success"?"bg-green-500":"bg-red-500"} text-white`,t.innerHTML=`
        <div class="flex items-center">
            <i class="fas fa-${a==="success"?"check":"exclamation"}-circle mr-2"></i>
            ${c}
        </div>
    `,document.body.appendChild(t),setTimeout(()=>{t.style.transition="opacity 0.5s",t.style.opacity="0",setTimeout(()=>t.remove(),500)},3e3)}window.showNotification=u;
