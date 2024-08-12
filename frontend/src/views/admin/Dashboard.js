import React from "react";
import { Redirect } from 'react-router-dom'
import { useAuth } from "../../providers/AuthContext";

export default function Dashboard() {
  const { token, logout } = useAuth();

  if (!token) {
    return <Redirect to="/login" />;
  }

  return (
    <>
      <div className="flex flex-wrap mt-4">
      </div>
    </>
  );
}
