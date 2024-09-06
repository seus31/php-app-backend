import React from "react";
import { BrowserRouter, Route, Switch, Redirect } from "react-router-dom";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "assets/styles/tailwind.css";
import Admin from "layouts/Admin.js";
import Auth from "layouts/Auth.js";
import { createRoot } from "react-dom/client";
import { ApiProvider } from "./providers/ApiContext";
import { AuthProvider } from "./providers/AuthContext";

const root = createRoot(document.getElementById("root"));
const apiUrl = process.env.REACT_APP_API_BASE_URL

root.render(
  <ApiProvider apiUrl={apiUrl}>
    <AuthProvider>
      <BrowserRouter>
        <Switch>
          <Route path="/admin" component={Admin} />
          <Route path="/auth" component={Auth} />
          <Redirect from="*" to="/auth/login" />
        </Switch>
      </BrowserRouter>
    </AuthProvider>
  </ApiProvider>
);
