document.addEventListener("DOMContentLoaded", function(e) {
  const d = document;
  const offset = new Date().getTimezoneOffset();
  const loading = d.querySelector('.loading');
  const _id = d.getElementById('invoice_id');  
  const id = _id ? _id.value : null;
  const link = (ln = d.getElementById('statusUrl')) ? ln.attributes.href.value : null;
  const url = link;
  let statusNum = null;

  const params = {method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'}};
  async function getStat() {
    params.body = JSON.stringify({'invoice':id});
    const response = await fetch(url, params);
    const result = await response.text();
    return result;
  }
  
  function setToggler() {
    const toggler = d.querySelector('.btn.toggler');
    if (toggler) {
      toggler.addEventListener('click', function(e) {
        d.querySelector('.status-data').classList.toggle('historyFlag');
      })
    }
  }
  
  async function load() {
    if (!url) { 
      return;
    }
    params.body = JSON.stringify({'invoice':id,'offset':offset});
    const response = await fetch(url, params);
    const result = await response.text();
    const wrapper = d.getElementById('__apn-invoice');
    wrapper.outerHTML = result;
    statusNum = (stn = d.getElementById('statusNum')) ? stn.value : 0;
    setCopy();
    setToggler();
    countdown();
    linkback();
    stat = await getStat();

    async function refresh() {
        if (statusNum === 0) { clearInterval(processId); return; }
        statusNum = await getStat();
        if (statusNum !== stat) { document.location.reload();}
    };
    if (statusNum > 0) { processId = setInterval(refresh, 5e3); }
  }
  
  function setCopy() {
    d.querySelectorAll('.btn__copy').forEach((e) => e.addEventListener('click', function(e) { c2c(this) }));
    function c2c(e) {
      let i = e.firstChild;
      if(navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(i.value);
      }
      else {
        i.type = "text"; i.focus(); i.select(); d.execCommand('copy'); i.type = "hidden";
      }
      e.classList.toggle('copied');
      setTimeout(function() {
          e.classList.toggle('copied');
      },1000, e);
    }
  }
  function countdown() {
    expire = d.getElementById('expire');
    if (expire === null || expire.value <=0) {
      return;
    }
    let counter = d.getElementById('countdown');
    if (counter === null) {
      return;
    }
    let distance = expire.value;
    const interval = setInterval(() => {
      const days = f2i(Math.floor(distance / (60 * 60 * 24))) ;
      const hours = f2i(Math.floor((distance % (60 * 60 * 24)) / (60 * 60)));
      const minutes = f2i(Math.floor((distance % (60 * 60)) / 60));
      const seconds = f2i(Math.floor((distance % (60))));
      counter.innerHTML = (days > 0 ? `${days}d `: '')
        + (hours > 0 ? `${hours}h `: '')
        + (minutes > 0 ? `${minutes}m `: '')
        + `${seconds}s`;
      distance -=1;
      if (distance < 0) {
        clearInterval(interval);
        counter.innerHTML = '';
        document.location.reload();
      }
    }, 1000);
  }
  function f2i (value) {
    return value | 0;
  }
  function linkback() {
    let linkback = d.getElementById('linkback');
    let counter = d.getElementById('linkback-counter');
    if (linkback === null || counter === null) {
      return;
    }
    let count = counter.innerHTML;
    let link = linkback.attributes.href.value;
    const interval = setInterval(() => {
      counter.innerHTML = count;
      count -=1;
      if (count == 0) {
        clearInterval(interval);
        d.location.href = link;
      }
    }, 1000);
  }
  load();

});
