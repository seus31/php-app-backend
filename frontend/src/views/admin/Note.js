import axios from 'axios'
import React, { useEffect, useState } from 'react'
import { Redirect } from 'react-router-dom'
import { useApi } from '../../providers/ApiContext'
import { useAuth } from '../../providers/AuthContext'
import NotesTable from "../../components/Tables/NotesTable";

export default function Note() {
  const apiUrl = useApi();
  const apiEndpoint = apiUrl + '/notes'
  const [page, setPage] = useState(1)
  const [perPage, setPerPage] = useState(15)
  const [notes, setNotes] = useState([])
  const { token, logout } = useAuth()
  const tableHeader = ['ID', '本文', '作成日'];

  useEffect(() => {
    if (!token) {
      return <Redirect to="/login"/>
    }

    const fetchItems = async () => {
      try {
        const response = await axios.get(apiEndpoint, {page: page, per_page: perPage}, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
        setNotes(response.data.data)
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    };

    fetchItems().then(r => {});
  }, []);

  return (
    <>
      <div className="flex flex-wrap mt-4">
        <NotesTable color="light" items={notes} />
      </div>
    </>
  )
}
