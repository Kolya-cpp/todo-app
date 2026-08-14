// UI NAVIGATION

const sidebar = document.querySelector(".sidebar");

const todayBlock = document.getElementById("todayBlock");
const weekBlock = document.getElementById("weekBlock");
const activeBlock = document.getElementById("activeBlock");
const doneBlock = document.getElementById("doneBlock");

const btnToday = document.getElementById("btnToday");
const btnWeek = document.getElementById("btnWeek");
const btnActive = document.getElementById("btnActive");
const btnDone = document.getElementById("btnDone");


// HIDE ALL BLOCKS

function hideAllBlocks() {

    todayBlock.style.display = "none";
    weekBlock.style.display = "none";
    activeBlock.style.display = "none";
    doneBlock.style.display = "none";

    btnToday.classList.remove("active");
    btnWeek.classList.remove("active");
    btnActive.classList.remove("active");
    btnDone.classList.remove("active");
}


// SHOW BLOCK

function showBlock(block, button) {

    hideAllBlocks();

    block.style.display = "block";
    button.classList.add("active");

}


// SIDEBAR CLICK

sidebar.addEventListener("click", function(event) {

    const button = event.target.closest(".sidebar-card");

    if (!button) {
        return;
    }


    // TODAY

    if (button.id === "btnToday") {

        showBlock(todayBlock, btnToday);

    }


    // WEEK

    else if (button.id === "btnWeek") {

        showBlock(weekBlock, btnWeek);

    }


    // ACTIVE

    else if (button.id === "btnActive") {

        showBlock(activeBlock, btnActive);

    }


    // DONE

    else if (button.id === "btnDone") {

        showBlock(doneBlock, btnDone);

    }

});


// TOAST

function showToast(text) {

    const toast = document.getElementById("toast");

    if (!toast) {
        return;
    }

    toast.innerText = text;

    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 2000);

}