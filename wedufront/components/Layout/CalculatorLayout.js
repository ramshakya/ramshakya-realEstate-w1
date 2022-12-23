import React from 'react'
import NavigationBar from "../Navbar/NavigationBar";
import Footer from "../Footer/Footer";
import Head from "./HeaderForCalculator"
export default function Layout(props) {
  const { title, children } = props;
  return (
    <div>
      <Head title={title} />       
      <NavigationBar />
      {children}
      <Footer />
    </div>
  );
}
