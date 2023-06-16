document.addEventListener('alpine:init', () => {
    Alpine.store({table: false, settings: false});
})
function table() {
  return {
    path: '/helpers/action_table.php',
    table: false,
    label: null,
    async load() {
      this.label = 'Creating...',
      url = (this.action) ? this.path + `?action=${this.action}` : this.path;
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
    path: '/helpers/action_settings.php',
    content: null,
    label: null,
    action: null,
    expand: false,
    async load() {
      this.label = 'Loading...',
      this.content = 'Settings file not exist. Press \'Crete settings\' button';
      url = (this.action) ? this.path + `?action=${this.action}` : this.path;
      data = await ajax(url);
      if(data !== false) { this.file = true; this.content = data;}
      else { this.file = false}
      this.label = this.file ? 'Settings already exist' : 'Creare settings';
      this.$store.settings = JSON.parse(data);
    },
    doAction() { this.action = this.file ? 'delete' : 'create'; this.load(); },
    toggle() {this.expand = !this.expand; console.log()}
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