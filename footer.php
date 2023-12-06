<footer class="text-light container-fluid w-100">
    <div class="container-fluid p-3 text-center">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-3">
                <h3 class="fw-bolder my-2">FOLLOW US</h3>
                <ul class="list-unstyled">
                    <li class="my-2"><a class="text-light" href="https://www.facebook.com" target="_blank">
                    <i class="fa-brands fs-2 fa-facebook fa-shake"></i>
                    </a></li>
                    <li class="my-2"><a class="text-light" href="https://www.instagram.com/" target="_blank">
                    <i class="fab fa-instagram fa-shake fs-2"></i>
                    </a></li>
                    <li class="my-2"><a class="text-light" href="https://www.linkedin.com/" target="_blank">
                    <i class="fab fa-linkedin fa-shake fs-2"></i>
                    </a></li>
                    <li class="my-2"><a class="text-light" href="https://twitter.com/home" target="_blank">
                    <i class="fab fa-twitter fa-shake fs-2"></i>
                    </a></li>
                </ul>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-9 align-self-center">
                <h1 class="display-1 fw-bolder">NEWS BLOGGER</h1>
            </div>
        </div>
        <small class="small m-auto">Copyrights &copy; NEWS-BLOGGER 2022-23 &reg;</small>
    </div>
</footer>
</body>
<script src="./src/static/js/popper.js"></script>
<script src="./src/static/js/bootstrap.js"></script>
<script>
    let scrollPrev = window.pageYOffset 
    let nav = document.getElementById('navbar')
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset
        if(scrollPrev > currentScroll){
            // nav.style.position = 'fixed'
            nav.classList.remove('hidden')
        }
        else {
            nav.classList.add('hidden')
        }
        scrollPrev = currentScroll
    })


    // Add Post Modal Ajax
    
    function addPost() {
        let title = document.getElementById('title').value;
        let desc = document.getElementById('desc').value;
        let category = document.getElementById('category').value;
        let fileImg = document.getElementById('fileToUpload').files[0];
        let approve = document.getElementById('approve').value;
        let headline = document.getElementById('headline').value;

        let formData = new FormData();
        formData.append('title', title);
        formData.append('desc', desc);
        formData.append('category', category);
        formData.append('fileToUpload', fileImg);
        formData.append('approve', approve);
        formData.append('headline', headline);
        formData.append('user_id', <?= $_SESSION['user_id'] ?>);
        formData.append('auth', 'add-post');

        xhttp.onreadystatechange = function(){
            let msgs = document.getElementById('addpost-msgs')
            if(this.readyState === 4 && this.status === 200){
                res = JSON.parse(this.response);
                if(res.status === 'error'){
                    msgs.innerHTML = `<div class='alert alert-danger'>${res.msg}</div>`;
                }
                else {
                    msgs.innerHTML = `<div class='alert alert-success'>${res.msg}</div>`;
                }
            }
        }

        xhttp.open('POST', 'auth.php');
        xhttp.send(formData);
    }
</script>
</html>