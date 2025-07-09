"use client"

import { useEffect, useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Users, FileText, Calendar, Settings, Plus, Edit, Trash2, Eye, BarChart3, Bell } from "lucide-react"

interface User {
  email: string
  role: string
  department?: string
}

export default function AdminDashboard() {
  const [user, setUser] = useState<User | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Check if user is logged in
    const userData = localStorage.getItem("user")
    if (userData) {
      const parsedUser = JSON.parse(userData)
      if (parsedUser.role === "admin") {
        setUser(parsedUser)
      } else {
        // Redirect non-admin users
        window.location.href = "/login"
      }
    } else {
      window.location.href = "/login"
    }
    setLoading(false)
  }, [])

  const handleLogout = () => {
    localStorage.removeItem("user")
    window.location.href = "/login"
  }

  if (loading) {
    return <div className="min-h-screen flex items-center justify-center">Loading...</div>
  }

  if (!user) {
    return null
  }

  const stats = [
    { title: "Total Students", value: "2,547", icon: Users, color: "text-blue-600" },
    { title: "Active News", value: "23", icon: FileText, color: "text-green-600" },
    { title: "Upcoming Events", value: "8", icon: Calendar, color: "text-purple-600" },
    { title: "Departments", value: "7", icon: Settings, color: "text-orange-600" },
  ]

  const recentNews = [
    { id: 1, title: "New Academic Year Guidelines", status: "Published", date: "2024-01-15" },
    { id: 2, title: "TESDA Program Updates", status: "Draft", date: "2024-01-14" },
    { id: 3, title: "Campus Facility Improvements", status: "Published", date: "2024-01-13" },
  ]

  const recentUsers = [
    { name: "CIT Department", email: "cit@sjcsi.edu.ph", role: "Department", lastLogin: "2024-01-15" },
    { name: "CBA Department", email: "cba@sjcsi.edu.ph", role: "Department", lastLogin: "2024-01-14" },
    { name: "Registrar Office", email: "registrar@sjcsi.edu.ph", role: "Office", lastLogin: "2024-01-13" },
  ]

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="container mx-auto px-4 py-4">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
              <p className="text-gray-600">Welcome back, {user.email}</p>
            </div>
            <div className="flex items-center space-x-4">
              <Button variant="outline" size="sm">
                <Bell className="h-4 w-4 mr-2" />
                Notifications
              </Button>
              <Button variant="outline" size="sm" onClick={handleLogout}>
                Logout
              </Button>
            </div>
          </div>
        </div>
      </header>

      <div className="container mx-auto px-4 py-8">
        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          {stats.map((stat, index) => (
            <Card key={index}>
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-gray-600 mb-1">{stat.title}</p>
                    <p className="text-3xl font-bold">{stat.value}</p>
                  </div>
                  <stat.icon className={`h-8 w-8 ${stat.color}`} />
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Main Content */}
        <Tabs defaultValue="overview" className="space-y-6">
          <TabsList>
            <TabsTrigger value="overview">Overview</TabsTrigger>
            <TabsTrigger value="news">News Management</TabsTrigger>
            <TabsTrigger value="users">User Management</TabsTrigger>
            <TabsTrigger value="settings">Settings</TabsTrigger>
          </TabsList>

          <TabsContent value="overview" className="space-y-6">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* Recent News */}
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center justify-between">
                    Recent News
                    <Button size="sm">
                      <Plus className="h-4 w-4 mr-2" />
                      Add News
                    </Button>
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {recentNews.map((news) => (
                      <div key={news.id} className="flex items-center justify-between p-3 border rounded-lg">
                        <div>
                          <h4 className="font-medium">{news.title}</h4>
                          <p className="text-sm text-gray-600">{news.date}</p>
                        </div>
                        <div className="flex items-center space-x-2">
                          <Badge variant={news.status === "Published" ? "default" : "secondary"}>{news.status}</Badge>
                          <Button size="sm" variant="ghost">
                            <Edit className="h-4 w-4" />
                          </Button>
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>

              {/* Quick Actions */}
              <Card>
                <CardHeader>
                  <CardTitle>Quick Actions</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-2 gap-4">
                    <Button className="h-20 flex-col">
                      <FileText className="h-6 w-6 mb-2" />
                      Create News
                    </Button>
                    <Button variant="outline" className="h-20 flex-col bg-transparent">
                      <Calendar className="h-6 w-6 mb-2" />
                      Add Event
                    </Button>
                    <Button variant="outline" className="h-20 flex-col bg-transparent">
                      <Users className="h-6 w-6 mb-2" />
                      Manage Users
                    </Button>
                    <Button variant="outline" className="h-20 flex-col bg-transparent">
                      <BarChart3 className="h-6 w-6 mb-2" />
                      View Reports
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="news">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  News Management
                  <Button>
                    <Plus className="h-4 w-4 mr-2" />
                    Create New Article
                  </Button>
                </CardTitle>
                <CardDescription>Manage news articles and announcements for the website</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {recentNews.map((news) => (
                    <div key={news.id} className="flex items-center justify-between p-4 border rounded-lg">
                      <div>
                        <h4 className="font-medium">{news.title}</h4>
                        <p className="text-sm text-gray-600">Published on {news.date}</p>
                      </div>
                      <div className="flex items-center space-x-2">
                        <Badge variant={news.status === "Published" ? "default" : "secondary"}>{news.status}</Badge>
                        <Button size="sm" variant="ghost">
                          <Eye className="h-4 w-4" />
                        </Button>
                        <Button size="sm" variant="ghost">
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button size="sm" variant="ghost">
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="users">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  User Management
                  <Button>
                    <Plus className="h-4 w-4 mr-2" />
                    Add User
                  </Button>
                </CardTitle>
                <CardDescription>Manage department and office account access</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {recentUsers.map((user, index) => (
                    <div key={index} className="flex items-center justify-between p-4 border rounded-lg">
                      <div>
                        <h4 className="font-medium">{user.name}</h4>
                        <p className="text-sm text-gray-600">{user.email}</p>
                        <p className="text-xs text-gray-500">Last login: {user.lastLogin}</p>
                      </div>
                      <div className="flex items-center space-x-2">
                        <Badge variant="outline">{user.role}</Badge>
                        <Button size="sm" variant="ghost">
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button size="sm" variant="ghost">
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="settings">
            <Card>
              <CardHeader>
                <CardTitle>System Settings</CardTitle>
                <CardDescription>Configure website settings and preferences</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-6">
                  <div>
                    <h4 className="font-medium mb-2">Website Configuration</h4>
                    <div className="space-y-2">
                      <Button variant="outline" className="w-full justify-start bg-transparent">
                        <Settings className="h-4 w-4 mr-2" />
                        General Settings
                      </Button>
                      <Button variant="outline" className="w-full justify-start bg-transparent">
                        <FileText className="h-4 w-4 mr-2" />
                        Content Management
                      </Button>
                      <Button variant="outline" className="w-full justify-start bg-transparent">
                        <Users className="h-4 w-4 mr-2" />
                        User Permissions
                      </Button>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  )
}
