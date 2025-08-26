class SelectComponent extends HTMLElement {
  static formAssociated = true;
  static get observedAttributes() {
    return ["options"]; // Attributes to watch
  }
  constructor() {
    super();
    this._options = [];
    this.attachShadow({ mode: 'open' });

    // Initialize state
    this.state = {
      options: [],
      selected: [],
      search: "",
      isOpen: false,
    };
    this.searchText = g.tr('Search', {es:'Buscar'})
    if (this.hasAttribute('placeholder')) {
      this.searchText = this.getAttribute("placeholder")
    }

    this.state.multiple = this.hasAttribute("multiple");
    this.readValue()

    // Render initial HTML
    this.optionsHTML = this.getSelectedOptions()
    this.render()

    this.dropdownToggle = this.shadowRoot.querySelector(".dropdown-toggle");
    this.dropdownOptions = this.shadowRoot.querySelector(".dropdown-options");
    this.searchInput = this.shadowRoot.querySelector(".search-input");
    this.optionsList = this.shadowRoot.querySelector(".options-list");
    this.selectedOptionsContainer = this.shadowRoot.querySelector(".selected-options");
    this.updateSelectedOptions()
    this.render();

  }

  render() {
    let _display=''
    if (this.state.selected.length>0) {
      _display='display:none'
    }
    this.shadowRoot.innerHTML = `
    <style>
      .dropdown {
        position: relative;
        font-family: Arial, sans-serif;
      }
      .dropdown-toggle {
        border: 1px solid #ccc;
        padding: 4px 10px;
        cursor: pointer;
        background-color: white;
      }
      .dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        border: 1px solid #ccc;
        background-color: white;
        max-height: 200px;
        overflow-y: auto;
        z-index: 9999999;
        display: none;
      }
      .dropdown-options.open {
        display: block;
      }
      .option {
        padding: 4px 8px;
        cursor: pointer;
      }
      .option:hover {
        background-color: #f0f0f0;
      }
      .search-input {
        padding: 6px 0;
        border-bottom: 1px solid #ccc;
        width:auto;
        margin: 4px 0;
        display:inline;
      }
      .selected-options {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 5px;
        padding: 4px 0;
      }
      .selected-option {
        background: lightgrey;
        color: black;
        padding: 4px 6px;
        border-radius: 14px;
        text-wrap-mode: nowrap;
        font-size: 90%;
      }
      .remove {
        margin-left: 2px;
        cursor: pointer;
        width:20px;
        height:20px;
        color:white;
        font-size:14px;
        border-radius:50%;
        background:orangered;
        display:inline-block;
        text-align:center;
      }
    </style>
    <div class="dropdown">
      <div class="dropdown-toggle" style="">
        <div class="selected-options">`+this.optionsHTML+`</div>
        <input type="text" class="search-input" autocomplete="off" style="color:black;font-size:1em;outline:none;width:60px;margin-left:4px;border:0;${_display}" placeholder="${this.searchText}..." />
        <div class="dropdown-options">
          <div class="options-list"> </div>
        </div>
      </div>
    </div>
  `;
  }

  connectedCallback() {
    // Attach event listeners
    this.dropdownToggle = this.shadowRoot.querySelector(".dropdown-toggle");
    this.dropdownOptions = this.shadowRoot.querySelector(".dropdown-options");
    this.searchInput = this.shadowRoot.querySelector(".search-input");
    this.optionsList = this.shadowRoot.querySelector(".options-list");
    this.selectedOptionsContainer = this.shadowRoot.querySelector(".selected-options");
    this.dropdownToggle.addEventListener("click", () => this.toggleDropdown());
    this.searchInput.addEventListener("input", (e) => this.handleSearch(e));
    this.readValue()
  }

  readValue() {
    let value = ''
    if (this.hasAttribute('value')) {
      value = this.getAttribute("value")
    }
    if (value && value!='' && value!=null) {
      let ids = []
      try {
        ids = JSON.parse(value)
      } catch (exception) {
        ids = [value]
      }
      if(!Array.isArray(ids)) {
        ids = [];
      }
      let id;
      for(id of ids) {
        this.state.selected = [{id:id, label:id}]
      }
    }
  }

  toggleDropdown() {
    this.state.isOpen = !this.state.isOpen;
    this.dropdownOptions.classList.toggle("open", this.state.isOpen);
    if (this.state.selected.length>0 && !this.state.isOpen) {
      this.searchInput.style.display='none'
    } else {
      this.searchInput.style.display='inline'
    }
    if (this.state.isOpen) {
      this.searchInput.value = ''
      this.searchInput.focus()
      this.state.search = ''
    }
    this.renderOptions()
  }

  handleSearch(event) {
    this.state.search = event.target.value.toLowerCase();
    this.renderOptions();
  }

  handleOptionClick(id,label) {
    let option = {id:id,label:label}
    this.addOption(option)
    if (this.state.multiple) {
      this.toggleDropdown();
    }
    this.updateSelectedOptions();
  }

  addOption(option) {
    if (this.state.multiple) {
      const index = this.state.selected.findIndex((item) => item.id === id);
      if (index === -1) {
        this.state.selected.push(option);
      } else {
        this.state.selected.splice(index, 1);
      }
    } else {
      this.state.selected = [option];
    }
  }

  updateSelectedOptions() {
    this.selectedOptionsContainer.innerHTML = this.getSelectedOptions();
  }

  getSelectedOptions() {
    return this.state.selected
      .map(
        (option) => `
          <span class="selected-option" type="botton">
            ${option.label}
            <span class="remove" onclick="this.getRootNode().host.removeOption('${option.id}',event)">&times;</span>
          </span>
        `
      )
      .join("");
  }

  removeOption(id, event) {
    const index = this.state.selected.findIndex((item) => item.id === id);
    event.preventDefault();
    if (index !== -1) {
      this.state.selected.splice(index, 1);
      this.updateSelectedOptions();
    }
  }

  renderOptions() {
    const filteredOptions = this.state.options.filter((option) =>
      option.label.toLowerCase().includes(this.state.search)
    );
    this.optionsList.innerHTML = filteredOptions
      .map(
        (option) => `
          <div class="option" onclick="this.getRootNode().host.handleOptionClick('${option.id}','${option.label}')">
            ${option.label}
          </div>
        `
      )
      .join("");
    if (this.optionsList.innerHTML=='') {
      this.optionsList.innerHTML = '&nbsp;'
    }
  }

  get value() {
    if (this.state.selected.length==0) {
      return null
    }
    if (this.state.multiple) {
      return this.state.selected.map(item => item.id);
    }
    return this.state.selected[0].id;
  }

  get name() {
    return this.getAttribute('name') || 'select-component';
  }

  attributeChangedCallback(name, oldValue, newValue) {
    if (name === "options") {
      let json = []
      try {
        json = JSON.parse(newValue);
      } catch (exception) {
        json = [];
      }
      this.state.options = json
      this.state.selected = []
      this.searchInput.style.display='inline'
      this.renderOptions();
      this.updateSelectedOptions();
    }
  }

}

// Register the custom element
customElements.define("select-component", SelectComponent);

/*
<select-component 
  options='[{"id":1, "label":"Apple"}, {"id":2, "label":"Banana"}, {"id":3, "label":"Cherry"}]' 
  placeholder="Choose a fruit" 
  multiple>
</select-component>
*/
