
// Page Elements
const addContactBtn = document.getElementById("add-contact");
const searchBar = document.getElementById("search");
const contactList = document.getElementById("contacts-list");
const modalTitle = document.getElementById("modal-title");
const modal = document.getElementById("contact-modal"); // may be modal-content
const form = document.getElementById("contact-form");
const saveContactBtn = document.getElementById("save-contact"); 
const cancelContactBtn = document.getElementById("cancel-contact");


// Form Information
const firstName = document.getElementById("first-name");
const lastName = document.getElementById("last-name");
const phoneNumber = documnet.getElementById("phone-number");
const email = document.getElementById("email");
const address = document.getElementById("address");

// Tracks all user contacts
let contacts = [];
let currentContactId = null;

document.addEventListener("DOMConentLoaded", () => {

    fetchContacts();

    addContactBtn.addEventListener('click', addContactModal);
    searchBar.addEventListener('input', search);
    form.addEventListener('submit', submitForm);
    cancelContactBtn.addEventListner('click', closeModal);

});

function fetchContacts(){
    fetch('../LAMPAPI/contact-endpts/readCon.php')
    .then(response => {
        if(response.ok){
            return response.json();
        }
        throw new Error('Failed to load Contacts'); // may be secuirty concern
    })
    .then(data => {
        contacts = data; // stores in our array
        renderContacts();
    })
    .catch(error => {
        alert('There was an issue loading contacts, please try again.');
        console.error('Error: ', error);
    });
}

function renderContacts(displayedContacts){
    contactList.innerHTML = "";

    if(displayedContacts.length === 0){
        contactList.innerHTML = "<p>No Contacts Found</p>";
    }

    displayedContacts.forEach(contact => {
        const contactDiv = document.createElement("div");
        contactDiv.className = 'contact-entry';
        contactDiv.dataSet.id = contact.id; // for delete

        contactDiv.innerHTML = `
            <h2>${contact.firstName} ${contact.lastName}</h2>
            <div class="contact-info">
                ${contact.phoneNumber ? `<p>Phone: ${contact.phoneNumber}</p>` : ''}
                ${contact.email ? `<p>Email: ${contact.email}</p>` : ''}
                ${contact.address ? `<p>Address: ${contact.address}</p>` : ''}
            </div>

            <div class="contact-actions">
                <button class="edit-btn">Edit</button>
                <button class="del-btn">Delete</button>
            </div>
        `;

        contactDiv.querySelector('.edit-btn').addEventListener('click', () => {
            editContactModal(contact);
        });

        contactDiv.querySelector('.del-btn').addEventListener('click', () => {
            // delete contact
        });

        contactList.appendChild(contactDiv);
    });
}

function addContactModal(){
    modalTitle.innerText = 'Add New Contact';
    saveContactBtn.innerText = 'Add Contact';
    currentContactId = null;
    form.reset();
    modal.style.display = 'block'; // makes modal visible
}

function editContactModal(contact){
    modalTitle.innerText = 'Edit Contact';
    saveContactBtn.innerText = 'Save Changes';
    currentContactId = contact.id;

    firstName.value = contact.firstName;
    lastName.value = contact.lastName;
    phoneNumber.value = contact.phoneNumber || '';
    email.value = contact.email || '';
    address.value = contact.address || '';

    modal.style.display = 'block'; // makes modal visible
}

function closeModal(){
    modal.style.display = 'none';
    form.reset();
}

function submitForm(event){
    event.preventDefault();

    const contactData = {
        firstName: firstName.value.trim(),
        lastName: lastName.value.trim(),
        phoneNumber: phoneNumber.value.trim(),
        email: email.value.trim(),
        address: address.value.trim()
    }

    if(currentContactId){
        // update contact
    } else {
        // create new contact
    }
}

function search(){
    const searchTerm = searchBar.value.toLowerCase().trim();

    if(!searchTerm){
        renderContacts(contacts);
        return;
    }

    fetch('../LAMPAPI/contact-endpts/readCon.php')
    .then(response => {
        if(response.ok){
            return response.json();
        }
        throw new Error('Failed to load Contacts'); // may be secuirty concern
    })
    .then(data => {
        contacts = data;
        renderContacts(contacts);
    })
    .catch(error => {
        alert('There was an issue loading contacts, please try again.'); // may be secuirty concern
        console.error('Error: ', error);
    });
}

//add contact
//update contact
//delete contact
