<style>
html,
body {
  height: 100%;
}

body {
  display: flex;
  align-items: center;
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #f5f5f5;
}

.form-signin {
  width: 100%;
  max-width: 330px;
  padding: 15px;
  margin: auto;
}

.form-signin .checkbox {
  font-weight: 400;
}

.form-signin .form-floating:focus-within {
  z-index: 2;
}

.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}

.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}

.bd-placeholder-img {
  font-size: 1.125rem;
  text-anchor: middle;
  -webkit-user-select: none;
  -moz-user-select: none;
  user-select: none;
}

@media (min-width: 768px) {
  .bd-placeholder-img-lg {
    font-size: 3.5rem;
  }
}
</style>

<div class="container">
<?= \Config\Services::validation()->listErrors() ?>

  <main class="form-signin text-center">
    <form method="post" id="ftmLogin" name="ftmLogin" action="/login/action">

    <?= csrf_field() ?>
    
      
      <h1 class="h3 mb-3 fw-normal"> 로그인 </h1>

      <div class="form-floating">
        <input type="text" class="form-control" id="floatingInput" name="id" placeholder="아이디">
        <label for="floatingInput">아이디</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="비밀번호">
        <label for="floatingPassword">비밀번호</label>
      </div>

      <button class="w-100 btn btn-lg btn-primary" type="submit">로그인</button>
    </form>
  </main>
</div>
