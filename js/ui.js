// UI NAVIGATION

const sidebar = document.querySelector(".sidebar");

const todayBlock = document.getElementById("todayBlock");
const tomorrowBlock = document.getElementById("tomorrowBlock");
const weekBlock = document.getElementById("weekBlock");
const activeBlock = document.getElementById("activeBlock");
const doneBlock = document.getElementById("doneBlock");
const nextWeekBlock = document.getElementById("nextWeekBlock");
const monthBlock = document.getElementById("monthBlock");
const nextMonthBlock = document.getElementById("nextMonthBlock");
const threeMonthsBlock = document.getElementById("3MonthsBlock");

const btnToday = document.getElementById("btnToday");
const btnTomorrow = document.getElementById("btnTomorrow");
const btnWeek = document.getElementById("btnWeek");
const btnActive = document.getElementById("btnActive");
const btnDone = document.getElementById("btnDone");
const btnNextWeek = document.getElementById("btnNextWeek");
const btnMonth = document.getElementById("btnMonth");
const btnNextMonth = document.getElementById("btnNextMonth");
const btn3Months = document.getElementById("btn3Months");

// HIDE ALL BLOCKS

function hideAllBlocks() {

    todayBlock.style.display = "none";
    tomorrowBlock.style.display = "none";
    weekBlock.style.display = "none";
    activeBlock.style.display = "none";
    doneBlock.style.display = "none";
    nextWeekBlock.style.display = "none";
    monthBlock.style.display = "none";
    nextMonthBlock.style.display = "none";
    threeMonthsBlock.style.display = "none";

    btnToday.classList.remove("active");
    btnTomorrow.classList.remove("active");
    btnWeek.classList.remove("active");
    btnNextWeek.classList.remove("active");
    btnMonth.classList.remove("active");
    btnNextMonth.classList.remove("active");
    btn3Months.classList.remove("active");
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

    // TOMORROW

    else if (button.id === "btnTomorrow") {

        showBlock(tomorrowBlock, btnTomorrow);

    }
    
    // WEEK

    else if (button.id === "btnWeek") {

        showBlock(weekBlock, btnWeek);

    }

    // NEXT WEEK

    else if (button.id === "btnNextWeek") {
    
        showBlock(nextWeekBlock, btnNextWeek);

    }
    
    // MONTH

    else if (button.id === "btnMonth") {

        showBlock(monthBlock, btnMonth);

    }
    
    // NEXT MONTH

    else if (button.id === "btnNextMonth") {

        showBlock(nextMonthBlock, btnNextMonth);

    }

    // 3 MONTHS

    else if (button.id === "btn3Months") {

        showBlock(threeMonthsBlock, btn3Months);

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