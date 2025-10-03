import React from 'react'
import DataDisplay from "@/components/DataDisplay";

const Dashboard = () => {
  return (
    <div className="min-h-screen bg-gray-100">
      <div className="container mx-auto py-8">
        <h1 className="text-3xl font-bold text-gray-800 mb-8 text-center">
          FFWS-JATIM Dashboard - Backend Integration Test
        </h1>
        <DataDisplay />
      </div>
    </div>
  )
}

export default Dashboard

