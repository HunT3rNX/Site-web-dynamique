const rangeInput = document.querySelectorAll(".range-input input"),
priceInput = document.querySelectorAll(".price-input input"),
range = document.querySelector(".slider .progress");
let priceGap = 10;
priceInput.forEach(input =>{
    input.addEventListener("input", e =>{
        let minPrice = parseInt(priceInput[0].value),
        maxPrice = parseInt(priceInput[1].value);
        
        if((maxPrice - minPrice >= priceGap) && maxPrice <= rangeInput[1].max){
            if(e.target.className === "input-min"){
                rangeInput[0].value = minPrice;
                range.style.left = ((minPrice / rangeInput[0].max) * 100) + "%";
            }else{
                rangeInput[1].value = maxPrice;
                range.style.right = 100 - (maxPrice / rangeInput[1].max) * 100 + "%";
            }
        }
    });
});
rangeInput.forEach(input =>{
    input.addEventListener("input", e =>{
        let minVal = parseInt(rangeInput[0].value),
        maxVal = parseInt(rangeInput[1].value);
        if((maxVal - minVal) < priceGap){
            if(e.target.className === "range-min"){
                rangeInput[0].value = maxVal - priceGap
            }else{
                rangeInput[1].value = minVal + priceGap;
            }
        }else{
            priceInput[0].value = minVal;
            priceInput[1].value = maxVal;
            range.style.left = ((minVal / rangeInput[0].max) * 100) + "%";
            range.style.right = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
        }
    });
});

  function dropdown(Name,ID) {
    var x = document.getElementById(Name);
    if(ID !== undefined ) var button = document.getElementById(ID);
    if (x.className.indexOf("w3-show") == -1) { 
      x.className += " w3-show";
      if(ID !== undefined ) button.className += " w3-theme-d5";
    } else {
      x.className = x.className.replace(" w3-show", "");
      if(ID !== undefined ) button.className = button.className.replace(" w3-theme-d5", "");
    }
  }

  function burgerMenu() {
    var x = document.getElementById("burgerMenu");
    if (x.className.indexOf("w3-show") == -1) { 
      x.className += " w3-show";
    } else {
      x.className = x.className.replace(" w3-show", "");
    }
  }

  function updateButton() {
    var x = document.getElementById("update");
    if (x.style.display != "inline") { 
      x.style.display = "inline";
    }
  }

  window.onscroll = function()
  {
    if (document.body.scrollTop > window.innerHeight/2 || document.documentElement.scrollTop > window.innerHeight/2) {
      document.getElementById("backToTop").style.display = "block";
    } else {
      document.getElementById("backToTop").style.display = "none";
    }
  }

  function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
  }