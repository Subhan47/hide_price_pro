@extends('layouts.default')

@section('styles')

@endsection

@section('content')
<section>
    <article>
         <div class="full-width align-right" style="margin-top: 1em;">
            <a href="#">Get Support</a>
            <a href="#" class="button secondary">User Gude</a>
            <a href="#" class="button" >Rules</a> 
         </div>
    </article>
</section>
<br/>
<hr/>



<section>
    <aside><h2>Base Price Of Product is Include</h2></aside>
    <article>
      <div class="card">
         <div class="row">
         </div>
      </div>
    </article>
</section>
<section>
    <aside><h2>Tax Rate(%)</h2></aside>
    <article>
      <div class="card">
         <div class="row">
         {!! Form::text('tax','',[
         'id'=>"tax"])
         !!}
         </div>
      </div>
    </article>
</section>
<section>
    <aside><h2>Show Dual Price Only For Taxable Products</h2></aside>
 <article>
   <div class="card">
    <div class="row">
      <select>
        <option >Yes</option>
        <option>No</option>
      </select>
      </div>
    </div>
  </article>
</section>
<section>
    <aside><h2>Price To Show</h2> </aside>
 <article>
   <div class="card">
        <div class="row">
          <select>
            <option>Both Price</option>
            <option>No</option>
          </select>
        </div>
      </div>
  </article>
</section>
<section>
    <aside> <h2>Size Ratio Between Prices</h2></aside>
  <article>
   <div class="card">
    <div class="row">
      <select>
        <option>1:1</option>
        <option>1:2</option>
      </select>
        </div>
      </div>
    </div>
  </article>
</section>
 <section>
    <aside><h2>Which Price Will Show First</h2></aside>
    <article>
        <div class="card">
         <div class="row">
            <select>
            <option>Tax include price</option>
            <option>1:2</option>
          </select>
        </div>
      </div>
    </article>
 </section>
 <section>
    <aside><h2>Tax Include Price Label</h2></aside>
    <article>
        <div class="card">
         <div class="row">
         {!! Form::text('labelInclude','',[
         'id'=>"priceColor"])
          !!}
         </div>
      </div>
    </article>
</section>
<section>
    <aside><h2>Tax Exclude Price Label</h2></aside>
    <article>
      <div class="card">
         <div class="row">
         {!! Form::text('labelexclude','',[
         'id'=>"priceColor"])
         !!}
         </div>
      </div>
    </article>
</section>
<section>
    <aside><h2>Tax Exclude Price Color</h2></aside>
    <article>
      <div class="card">
         <div class="row">
         {!! Form::color('colorinclude','',[
         'id'=>"priceColor"])
         !!}
         </div>
      </div>
    </article>
</section>
<section>
    <aside><h2>Tax Exclude Price Color</h2></aside>
    <article>
      <div class="card">
         <div class="row">
         {!! Form::color('colorexclude','',[
         'id'=>"priceColor"])
         !!}
         </div>
      </div>
    </article>
</section>
<button class="button">Save</button>

<footer>
  <article class="help">
    <span></span>
    <p>Support please log more<a href="#">Here</a>Email at<a href="#">Shopify@gmail.com</a></p>
  </article>
</footer>

@endsection

@section('scripts')
    @parent

    @if(config('shopify-app.appbridge_enabled'))
        <script>
            actions.TitleBar.create(app, { title: 'Welcome' });
        </script>
    @endif
@endsection
