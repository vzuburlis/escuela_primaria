class PasswordInput extends HTMLElement {
    constructor()
    {
        super();
        // Attach shadow DOM
        this.attachShadow({ mode: 'open' });

        // Create the HTML structure
        const wrapper = document.createElement('div');
        wrapper.style.display = 'flex';
        wrapper.style.alignItems = 'center';
        wrapper.style.position = 'relative';

        this.input = document.createElement('input');
        this.input.type = 'password';
        this.input.placeholde = 'password';
        this.input.style.paddingRight = '30px';
        this.input.style.width = '100%';
        wrapper.appendChild(this.input);

        const toggleIcon = document.createElement('span');
        toggleIcon.textContent = '👁'; // Eye icon
        toggleIcon.style.position = 'absolute';
        toggleIcon.style.right = '10px';
        toggleIcon.style.cursor = 'pointer';
        wrapper.appendChild(toggleIcon);

        // Style the component
        const style = document.createElement('style');
        style.textContent = `
            input {
                font - size: 16px;
                padding: 8px;
                border: 1px solid #ccc;
                border - radius: 4px;
                outline: none;
        }
            input:focus {
                border - color: #007BFF;
                box - shadow: 0 0 4px rgba(0, 123, 255, 0.25);
        }
            span {
                font - size: 18px;
                user - select: none;
        }
        `;

        // Append everything to the shadow DOM
        this.shadowRoot.append(style,wrapper);

        // Add event listener for toggling password visibility
        toggleIcon.addEventListener('click', () => {
            if (this.input.type === 'password') {
                this.input.type = 'text';
                toggleIcon.textContent = '🙈'; // Closed-eye icon
            } else {
                this.input.type = 'password';
                toggleIcon.textContent = '👁'; // Eye icon
            }
        });
    }

    static get observedAttributes()
    {
        return ['name', 'value', 'id'];
    }

    attributeChangedCallback(name, oldValue, newValue)
    {
        if (name === 'value') {
            this.input.value = newValue;
        }
    }

    connectedCallback()
    {
        // Initialize the attributes
        if (this.hasAttribute('name')) {
            this.input.name = this.getAttribute('name');
            this.setAttribute('name', null);
        }
        if (this.hasAttribute('value')) {
            this.input.value = this.getAttribute('value');
        }
        if (this.hasAttribute('id')) {
            this.input.id = this.getAttribute('id');
            this.id = ''
        }
    }
}

// Define the custom element
customElements.define('password-input', PasswordInput);
