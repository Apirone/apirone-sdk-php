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
      this.content = 'Settings file not exist. Press \'Crete settings\' button';
      url = (this.action) ? path + `?action=${this.action}` : path;
      data = await ajax(url);
      if(data !== false) { this.file = true; this.content = data;}
      else { this.file = false}
      this.label = this.file ? 'Settings already exist' : 'Creare settings';
      this.$store.settings = JSON.parse(data);
    },
    doAction() { this.action = this.file ? 'delete' : 'create'; this.load(); },
    toggle() {this.expand = !this.expand;}
  }
}

function playground() {
  return {
    label: 'Create',
    invoice: null,
    content: 'Invoice not created yet.',
    data: {currency: null, amount: null, lifetime: null, callbackUrl: null},
    expand: false,
    qrOnly: false,
    async create() {
      this.label = 'Creating...'; 
      url = '/helpers/action_invoice.php?data=' + JSON.stringify(this.data);
      result = await ajax(url);
      if(result) { this.invoice = result; this.content = JSON.stringify(result, null, 2); }
      this.label = 'Create'; 
    },
    render() {
      qr = this.qrOnly ? '&qr-only=1' : '';
      window.open('/render.php?invoice=' + this.invoice.invoice + qr, '_blank').focus();
    },
    toggle() {this.expand = !this.expand;}
  }
}

async function ajax(url) {
  data = false;
  form = new FormData();                                  
  await fetch(url, {
      method: 'POST',
      headers: {
          'X-Requested-With': 'XMLHttpRequest',
      }, 
      body: form
  })
  .then(response => response.json())
  .then(response => data = response)
  .catch(e => console.log('AJAX Error: ', e));
  return data;
}