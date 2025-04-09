
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
const phoneNumber = document.getElementById("phone-number");
const email = document.getElementById("email");
const address = document.getElementById("address");

// Tracks all user contacts
let contacts = [];
let currentContactId = null;

document.addEventListener("DOMContentLoaded", () => {

    const userId = localStorage.getItem('userId');
    const isLoggedIn = localStorage.getItem('isLoggedIn');

    if(!userId || !isLoggedIn){
        alert('Please Login to view contacts!');
        window.location.href = '../index.html';
    }

    fetchContacts();

    addContactBtn.addEventListener('click', addContactModal);
    searchBar.addEventListener('input', search);
    form.addEventListener('submit', submitForm);
    cancelContactBtn.addEventListener('click', closeModal);

});

function fetchContacts(){
    const userId = localStorage.getItem('userId');

    fetch(`../LAMPAPI/contact-endpts/readCon.php?userId=${userId}`)
    .then(response => {
        if(response.ok){
            return response.json();
        }
        throw new Error('Failed to load Contacts'); // may be secuirty concern
    })
    .then(data => {
        contacts = data; // stores in our array
        renderContacts(contacts);
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
        return;
    }

    displayedContacts.forEach(contact => {
        const contactDiv = document.createElement("div");
        contactDiv.className = 'contact-entry';
        contactDiv.dataset.id = contact.id; // for delete

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
            delContact(contact.id);
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
        updateContact(currentContactId, contactData);
    } else {
        createContact(contactData);
    }
}

function search(){
    const searchTerm = searchBar.value.toLowerCase().trim();
    const userId = localStorage.getItem('userId');

    if(!searchTerm){
        renderContacts(contacts);
        return;
    }

    const url = searchTerm 
        ? `../LAMPAPI/contact-endpts/searchCon.php?userId=${userId}&search=${encodeURIComponent(searchTerm)}` 
        : `../LAMPAPI/contact-entpts/readCon.php?userId=${userId}`;

    fetch(url)
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

function createContact(contactData){

    contactData.userId = localStorage.getItem('userId');

    fetch('../LAMPAPI/contact-endpts/createCon.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(contactData)
    })
    .then(response => {
        if(response.ok){
            return response.json();
        }
        throw new Error('Failed to Create Contact'); // may be secuirty concern
    })
    .then(data => {
        contacts.push(data);
        renderContacts(contacts);
        closeModal();
        alert('Contact Created!');
    })
    .catch(error => {
        alert('There was an issue creating the contact');
        console.error('Error: ', error);
    });
}

function updateContact(id, contactData){

    contactData.id = id;
    contactData.userId = localStorage.getItem('userId');

    fetch('../LAMPAPI/contact-endpts/updateCon.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(contactData)
    })
    .then(response => {
        if(response.ok){
            return response.json();
        }
        throw new Error('Failed to Update Contact'); // may be secuirty concern
    })
    .then(data => {

        const index = contacts.findIndex(contact => contact.id === id);
        if(index !== -1){
            contacts[index] = data;
        }

        renderContacts(contacts);
        closeModal();
        alert('Contact Updated!');
    })
    .catch(error => {
        alert('There was an issue updating the contact');
        console.error('Error: ', error);
    });
}

function delContact(id){

    const data = {
        id: id,
        userId: localStorage.getItem('userId')
    }

    fetch('../LAMPAPI/contact-endpts/deleteCon.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if(response.ok){
            return response.json();
        }
        throw new Error('Failed to Delete Contact'); // may be secuirty concern
    })
    .then(data => {
        contacts = contacts.filter(contact => contact.id !== id);
        renderContacts(contacts);
        alert('Contact Deleted!');
    })
    .catch(error => {
        alert('There was an issue deleting the contact');
        console.error('Error: ', error);
    });
}