let active_ep=null;
let active_gate=null;
let active_media=null;
function switch_ep(epid){
  let ep = eps.find((x) => { return x.epid === epid; });
  if(ep) {
    active_ep = ep;

    let ul_playgate = document.getElementById("playgate");
    ul_playgate.innerHTML="";

    let gates = ep['gate'];
    for(let g in gates) {
      ul_playgate.innerHTML += "<li class='pl-gate' data-pgid='"+gates[g]['_id']['$oid']+"' onclick='switch_gate(\""+gates[g]['_id']['$oid']+"\")'>线路"+(parseInt(g)+1)+"</li>";
    }

    if(gates){
      document.getElementById("playgate_tip").style.display = gates.length<2 ? 'none' : '';
      document.getElementById("playgate").style.display = gates.length<2 ? 'none' : '';
      active_gate = gates[0];
      loadscreen();
    }else{
      document.getElementById("playgate_tip").style.display = 'none';
      document.getElementById("playgate").style.display = 'none';
    }
  }
}
function switch_gate(pgid){
  if(active_ep) {
    active_gate = active_ep['gate'].find((x)=>{return x['_id']['$oid'] === pgid;});
    loadscreen();
  }
}
function loadscreen (){
  if(active_ep && active_gate){
    let playurl = active_gate['gateurl']+active_ep['url'];
    document.getElementById("screenfrm").setAttribute('src', playurl);
    activeclass();
  }
}
function activeclass (){
  let last_active = document.getElementsByClassName("active");
  if(last_active && last_active.length>0){
    for(var last=0; last < last_active.length;){
      last_active[last].classList.remove("active");
    }
  }
  if(active_ep){
    let tmpel = document.getElementsByClassName("eps-epli");
    for(let el of tmpel){
      if(el.getAttribute("data-epid") === active_ep.epid){
        el.classList.add('active');
      }
    }
  }
  if(active_gate){
    let tmpel = document.getElementsByClassName("pl-gate");
    for(let el of tmpel){
      if(el.getAttribute("data-pgid") === active_gate._id.$oid){
        el.classList.add('active');
      }
    }
  }
}
window.onload = function () {
  let defep = document.getElementById('eps_ul').lastElementChild;
  if(defep){
    switch_ep(defep.getAttribute("data-epid"));
  }
};