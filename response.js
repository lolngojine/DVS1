/*function showAdmin()
{
    const password= prompt("Enter Password to continue:");
    if(password==="simba1234")
    {
        document.getElementById("admin-section").style.display = "block";
    }
    else
    {
        alert("Incorrect  Password! Contact Dantebrave");
    }
}

*/


function showAdmin() {
  //  Get password from user
  const password = prompt("Enter Admin Password:");
  
  // Verify password
  if(password === "Simba1234")

    
     {
      // Redirect to admin page
      window.location.href = "admin.php";
  } else if (password !== null) {
    
      alert("Incorrect Password! Contact Dantebrave");
  }

}



  