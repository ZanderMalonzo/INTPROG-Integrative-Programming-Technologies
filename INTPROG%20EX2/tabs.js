const tabs= document.querySelectorAll('.tab_btn')
            const all_content= document.querySelectorAll('.info')

            tabs.forEach((tab, index)=>{
                tab.addEventListener('click',()=>{
                    tabs.forEach(tab=>{tab.classList.remove('active')});
                    tab.classList.add('active');

                all_content.forEach(content=>{content.classList.remove('active')});
                all_content[index].classList.add('active');


                })
            })


            function openModal(id) {
                document.getElementById(id).style.display = "flex";
            }

            function closeModal(id) {
                document.getElementById(id).style.display = "none";
            }