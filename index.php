<!DOCTYPE html>
<html>
<head>
  <title>Revplit password generator</title>    
  <meta name="viewport" content="width=device-width" />
  <link rel="icon" href="images/favicon.png" sizes="32x32">
  <link rel="stylesheet" type="text/css" href="css/styles.css?1">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.3/vue.min.js"></script>

  <!-- Google Analytics -->
   <script type="text/rocketscript">
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-73557879-1', 'auto');
      ga('send', 'pageview');

    </script>

</head>

<body>
  <div id="app">
    <section class="wrapper">   
      <h1>The Password Genie</h1>
      <div class="password-box">
        <span id="password" class="password" v-on:click="copyToClipboard">{{ password }}</span>
        <span class="regenerate-password" v-on:click="generatePassword"></span>
        <span class="copy-password" v-on:click="copyToClipboard"></span>
        <span class="tooltip" v-if="copied">Password copied successfuly!</span>
      </div>
      <form @keydown.enter.prevent="">
        <div class="field-wrap">
          <label>Strength</label>
          <span class="range-value">{{strength.text}}</span>
          <div class="range-slider_wrapper slider-strength" v-bind:class="strength.text">
            <span class="slider-bar" v-bind:style="{ width: strength.score + '%' }"></span>
            <input type="range" class="range-slider" min="0" max="100" v-model="strength.score" disabled>
          </div>  
        </div>
        <div class="seperator"></div>
        <div class="field-wrap">
          <label>Length</label>
          <span class="range-value">{{settings.length}}</span>
          <div class="range-slider_wrapper">
            <span class="slider-bar" v-bind:style="{ width: lengthThumbPosition + '%' }"></span>
            <input type="range" class="range-slider" min="6" v-bind:max="settings.maxLength" v-model="settings.length">
          </div>  
        </div>
        <div class="field-wrap">  
          <label>Digits</label>
          <span class="range-value">{{settings.digits}}</span>
          <div class="range-slider_wrapper">
            <span class="slider-bar"  v-bind:style="{ width: digitsThumbPosition + '%' }"></span>
            <input type="range" class="range-slider" min="0" v-bind:max="settings.maxDigits" v-model="settings.digits">
          </div>  
        </div>
        <div class="field-wrap">  
          <label>Symbols</label>
          <span class="range-value">{{settings.symbols}}</span>
          <div class="range-slider_wrapper">
            <span class="slider-bar"  v-bind:style="{ width: symbolsThumbPosition + '%' }"></span>
            <input type="range" class="range-slider" min="0" v-bind:max="settings.maxSymbols" v-model="settings.symbols">
          </div>  
        </div>
      </form>
    </section>
  </div>
  <footer>
    <div class="github-links">
      <a class="github-button" href="https://github.com/Revplit" aria-label="Follow @Mootje on GitHub">Follow @Mootje</a>
      <a class="github-button" href="https://github.com/Ryonoo" aria-label="Follow @Ryonoo on GitHub">Follow @Ryonoo</a>
      <a class="github-button" href="https://github.com/Revplit-team/password-generator-js" data-icon="octicon-star" aria-label="Star Revplit-team/password-generator-js on GitHub">Star</a>
    </div>  
    Made with <3 by <a href="https://revplit.com/">Revplit</a><br>
    View on <a href="https://github.com/Revplit-team/password-generator-js">Github</a>.
  </footer>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
<script>
new Vue({
  el: '#app',
  data() {
    return {
      password: '',
      copied: false,
      settings: {
        maxLength: 64,
        maxDigits: 10,
        maxSymbols: 10,
        length: 12,
        digits: 4,
        symbols: 2,
        ambiguous: true,
      }
    };
  },
  computed: {
    lengthThumbPosition: function() {
      return (( (this.settings.length - 6) / (this.settings.maxLength - 6)) * 100);
    },
    digitsThumbPosition: function() {
      return (( (this.settings.digits - 0) / (this.settings.maxDigits - 0)) * 100);
    },
    symbolsThumbPosition: function() {
      return (( (this.settings.symbols - 0) / (this.settings.maxSymbols - 0)) * 100);
    },
    strength: function() {
      var count = {
        excess: 0,
        upperCase: 0,
        numbers: 0,
        symbols: 0
      };
      var weight = {
        excess: 3,
        upperCase: 4,
        numbers: 5,
        symbols: 5,
        combo: 0, 
        flatLower: 0,
        flatNumber: 0
      };
      var strength = {
        text: '',
        score: 0
      };
      
      var baseScore = 30;
      for (i=0; i < this.password.length;i++){
        if (this.password.charAt(i).match(/[A-Z]/g)) {count.upperCase++;}
        if (this.password.charAt(i).match(/[0-9]/g)) {count.numbers++;}
        if (this.password.charAt(i).match(/(.*[!,@,#,$,%,^,&,*,?,_,~])/)) {count.symbols++;} 
      }
      
      count.excess = this.password.length - 6;
      
      if (count.upperCase && count.numbers && count.symbols){
        weight.combo = 25; 
      }
      else if ((count.upperCase && count.numbers) || (count.upperCase && count.symbols) || (count.numbers && count.symbols)){
        weight.combo = 15; 
      }
      
      if (this.password.match(/^[\sa-z]+$/))
      { 
        weight.flatLower = -30;
      }
      
      if (this.password.match(/^[\s0-9]+$/))
      { 
        weight.flatNumber = -50;
      }
      var score = 
        baseScore + 
        (count.excess * weight.excess) + 
        (count.upperCase * weight.upperCase) + 
        (count.numbers * weight.numbers) + 
        (count.symbols * weight.symbols) + 
        weight.combo + weight.flatLower + 
        weight.flatNumber;
      if(score < 30 ) {
        strength.text = "weak";
        strength.score = 10;
        return strength;
      } else if (score >= 30 && score < 75 ){
        strength.text = "average";
        strength.score = 40;
        return strength;
      } else if (score >= 75 && score < 150 ){
        strength.text = "strong";
        strength.score = 75;
        return strength;
      } else {
        strength.text = "secure";
        strength.score = 100;
        return strength;
      }
    },
  },
  mounted() {
    this.generatePassword();
  },
  watch: {
    settings: {
      handler: function() {
        this.generatePassword();
      },
      deep: true
    }
  },
  methods: {
    // copy password to clipboard
    copyToClipboard(){
      // we should create a textarea, put the password inside it, select it and finally copy it
      var copyElement = document.createElement("textarea");
      copyElement.style.opacity = '0';
      copyElement.style.position = 'fixed';
      copyElement.textContent = this.password;
      var body = document.getElementsByTagName('body')[0];
      body.appendChild(copyElement);
      copyElement.select();
      document.execCommand('copy');
      body.removeChild(copyElement);
      
      this.copied = true;
      // reset this.copied
      setTimeout(() => {
        this.copied = false;
      }, 750);
    },
    // generate the password
    generatePassword() {
      var lettersSetArray = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
      var symbolsSetArray = [ "=","+","-","^","?","!","%","&","*","$","#","^","@","|"];
      //var ambiguousSetArray = ["(",")","{","}","[","]","(",")","/","~",";",":",".","<",">"];
      var passwordArray = [];
      var digitsArray = [];
      var digitsPositionArray = [];
      // first, fill the password array with letters, uppercase and lowecase
      for (var i = 0; i < this.settings.length; i++) {
        // get an array for all indexes of the password array
        digitsPositionArray.push(i);
        var upperCase = Math.round(Math.random() * 1);
        if (upperCase === 0) {
          passwordArray[i] = lettersSetArray[Math.floor(Math.random()*lettersSetArray.length)].toUpperCase();
        }
        else {
          passwordArray[i] = lettersSetArray[Math.floor(Math.random()*lettersSetArray.length)];
        }
      }
      // Add digits to password
      for (i = 0; i < this.settings.digits; i++) {
        digit = Math.round(Math.random() * 9);
        numberIndex = digitsPositionArray[Math.floor(Math.random()*digitsPositionArray.length)];
        passwordArray[numberIndex] =  digit;
        /* remove position from digitsPositionArray so we make sure to the have the exact number of digits in our password
        since without this step, numbers may override other numbers */
        var j = digitsPositionArray.indexOf(numberIndex);
        if(i != -1) {
          digitsPositionArray.splice(j, 1);
        }
      }
      // add special characters "symbols"
      for (i = 0; i < this.settings.symbols; i++) {
        var symbol = symbolsSetArray[Math.floor(Math.random()*symbolsSetArray.length)];
        var symbolIndex = digitsPositionArray[Math.floor(Math.random()*digitsPositionArray.length)];
        passwordArray[symbolIndex] =  symbol;
        /* remove position from digitsPositionArray so we make sure to the have the exact number of digits in our password
        since without this step, numbers may override other numbers */
        var j = digitsPositionArray.indexOf(symbolIndex);
        if(i != -1) {
          digitsPositionArray.splice(j, 1);
        }
      }
      this.password = passwordArray.join("");
    },
  },
});
</script>
