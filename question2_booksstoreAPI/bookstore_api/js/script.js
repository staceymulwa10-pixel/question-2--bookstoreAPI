let currentPage = 1;
loadBooks();
function searchBooks(){

let keyword=document.getElementById("search").value.toLowerCase();

let rows=document.querySelectorAll("#bookTable tr");

rows.forEach(row=>{

let text=row.innerText.toLowerCase();

if(text.includes(keyword)){

row.style.display="";

}else{

row.style.display="none";

}

});

}
document.getElementById("bookForm").addEventListener("submit", function(e){

    e.preventDefault();

    let id = document.getElementById("bookId").value;

    if(id==""){

        // ADD NEW BOOK

        let formData = new FormData();

        formData.append("title", document.getElementById("title").value);
        formData.append("author", document.getElementById("author").value);
        formData.append("price", document.getElementById("price").value);
        formData.append("stock", document.getElementById("stock").value);

        fetch("api/books.php",{
            method:"POST",
            body:formData
        })

        .then(response=>response.json())

        .then(data=>{

            alert(data.message);

            document.getElementById("bookForm").reset();

            loadBooks();

        });

    }else{

        // UPDATE BOOK

        let formData = new URLSearchParams();

        formData.append("id", id);
        formData.append("title", document.getElementById("title").value);
        formData.append("author", document.getElementById("author").value);
        formData.append("price", document.getElementById("price").value);
        formData.append("stock", document.getElementById("stock").value);

        fetch("api/books.php",{

            method:"PUT",

            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },

            body:formData.toString()

        })

        .then(response=>response.json())

        .then(data=>{

            alert(data.message);

            document.getElementById("bookForm").reset();

            document.getElementById("bookId").value="";

            document.getElementById("submitBtn").innerHTML="Add Book";

            loadBooks();

        });

    }

});
function loadBooks(){

fetch("api/books.php?page=" + currentPage)

.then(response=>response.json())

.then(data=>{

let output="";

data.forEach(book=>{

output+=`

<tr>

<td>${book.id}</td>

<td>${book.title}</td>

<td>${book.author}</td>

<td>${book.price}</td>

<td>${book.stock}</td>

<td>

<button onclick="editBook(${book.id})">

Edit

</button>

<button onclick="deleteBook(${book.id})">

Delete

</button>

</td>

</tr>

`;

});

document.getElementById("bookTable").innerHTML=output;

});

}
function sortBooks(){

let rows=Array.from(document.querySelectorAll("#bookTable tr"));

let order=document.getElementById("sortPrice").value;

rows.sort(function(a,b){

let priceA=parseFloat(a.cells[3].innerText);

let priceB=parseFloat(b.cells[3].innerText);

if(order=="asc"){

return priceA-priceB;

}else{

return priceB-priceA;

}

});

let table=document.getElementById("bookTable");

table.innerHTML="";

rows.forEach(row=>table.appendChild(row));

}
function deleteBook(id){

if(confirm("Are you sure you want to delete this book?")){

fetch("api/books.php",{

method:"DELETE",

headers:{

"Content-Type":"application/x-www-form-urlencoded"

},

body:"id="+id

})

.then(response=>response.json())

.then(data=>{

alert(data.message);

loadBooks();

});

}

}
function editBook(id){

fetch("api/books.php")

.then(response=>response.json())

.then(data=>{

let book=data.find(item=>item.id==id);

document.getElementById("bookId").value=book.id;

document.getElementById("title").value=book.title;

document.getElementById("author").value=book.author;

document.getElementById("price").value=book.price;

document.getElementById("stock").value=book.stock;

document.getElementById("submitBtn").innerHTML="Update Book";

});

}
function changePage(step){

    currentPage += step;

    if(currentPage < 1){

        currentPage = 1;

    }

    document.getElementById("pageNumber").innerHTML = currentPage;

    loadBooks();

}