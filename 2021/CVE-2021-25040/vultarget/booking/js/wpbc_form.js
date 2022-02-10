class App extends React.Component {
  render() {
    return (
      <div className="App">
        <Header />
      </div>
    )
  }
}


class Header  extends React.Component {
  render() {
    return (
        <h1 style={{color:'#f00'}}>Ok,  we are using Rect  here !</h1>
    )
  }
}

jQuery( document ).ready( function (){
  ReactDOM.render( <App/>, document.getElementById( 'booking_form_div4' ) );
} );
