document.addEventListener('alpine:init', () => {
    Alpine.store({table: false, settings: false});
})
function table() {
  return {
    table: false,
    label: null,
    async load() {
      path = '/helpers/action_table.php',
      this.label = 'Creating...',
      url = (this.action) ? path + `?action=${this.action}` : path;
      data = await ajax(url);
      this.table = data;
      this.label = this.table ? 'Table already exist' : 'Create data table';
      this.$store.table = this.table;
    },
    doAction() { this.action = this.table ? 'delete' : 'create'; this.load(); },
  }
}
function settings() {
  return {
    file: false,
    content: null,
    label: null,
    action: null,
    expand: false,
    async load() {
      path = '/helpers/action_settings.php',
      this.label = 'Loading...',
      this.content = 'Settings file not exist. Press \'Create settings\' button';
      url = (this.action) ? path + `?action=${this.action}` : path;
      data = await ajax(url);
      if(data !== false) { this.file = true; this.content = data;}
      else { this.file = false}
      this.label = this.file ? 'Settings already exist' : 'Create settings';
      this.$store.settings = JSON.parse(data);
    },
    doAction() { this.action = this.file ? 'delete' : 'create'; this.load(); },
    toggle() {this.expand = !this.expand;}
  }
}

function playground() {
  return {
    label: 'Create invoice',
    invoice: null,
    content: 'Invoice not created yet.',
    data: {currency: null, amount: null, lifetime: null, expire: null, callbackUrl: null, linkback: null},
    userData: {title: null, merchant: null, url:null, price: null, 'sub-price': null, items: [], extras: []},
    expand: false,
    qrOnly: false,
    showUserData: false,
    async create() {
      this.label = 'Creating...';
      form = new FormData();
      form.append('data', JSON.stringify(this.data));

      if (this.showUserData) {
        form.append('userData', JSON.stringify(this.userData));
      }
      result = await ajax('/helpers/action_invoice.php', form);
      if(result) { this.invoice = result; this.content = JSON.stringify(result, null, 2); }
      this.label = 'Create'; 
    },
    render() {
      if (this.invoice == null) {return;}
      qr = this.qrOnly ? '&qr-only=1' : '';
      window.open('/render.php?invoice=' + this.invoice.invoice + qr, '_blank').focus();
    },
    toggle() {
      this.expand = !this.expand;
    },
    addItem() {
      this.userData.items.push({name: null, cost: null, qty: null, total:null});
    },
    addExtra() {
      this.userData.extras.push({name: null, price:null});
    }
  }
}

async function ajax(url, form=false) {
  data = false;
  if (!form) {
    form = new FormData();                                  
  }
  // console.log(JSON.stringify(Object.fromEntries(form)));
  await fetch(url, {
      method: 'POST',
      headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'charset':'UTF-8',
      }, 
      body: form
  })
  .then(response => response.json())
  .then(response => data = response)
  .catch(e => console.log('AJAX Error: ', e));
  return data;
}