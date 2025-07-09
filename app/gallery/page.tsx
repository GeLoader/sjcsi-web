"use client"

import { useState } from "react"
import { Card, CardContent } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Dialog, DialogContent, DialogTrigger } from "@/components/ui/dialog"
import { Play, ImageIcon, Calendar } from "lucide-react"
import Image from "next/image"

export default function GalleryPage() {
  const [selectedCategory, setSelectedCategory] = useState("all")

  const galleryItems = [
    {
      id: 1,
      title: "Graduation Ceremony 2024",
      category: "events",
      type: "image",
      thumbnail: "/placeholder.svg?height=300&width=400",
      date: "2024-03-15",
      description: "Annual graduation ceremony celebrating our graduates' achievements",
    },
    {
      id: 2,
      title: "Science Laboratory",
      category: "facilities",
      type: "image",
      thumbnail: "/placeholder.svg?height=300&width=400",
      date: "2024-01-20",
      description: "State-of-the-art science laboratory for hands-on learning",
    },
    {
      id: 3,
      title: "Campus Tour Video",
      category: "campus",
      type: "video",
      thumbnail: "/placeholder.svg?height=300&width=400",
      date: "2024-02-10",
      description: "Virtual tour of our beautiful campus facilities",
    },
    {
      id: 4,
      title: "Student Activities Fair",
      category: "events",
      type: "image",
      thumbnail: "/placeholder.svg?height=300&width=400",
      date: "2024-01-25",
      description: "Students showcasing various clubs and organizations",
    },
    {
      id: 5,
      title: "Library and Study Areas",
      category: "facilities",
      type: "image",
      thumbnail: "/placeholder.svg?height=300&width=400",
      date: "2024-01-15",
      description: "Modern library with extensive resources and quiet study spaces",
    },
    {
      id: 6,
      title: "Sports Festival Highlights",
      category: "events",
      type: "video",
      thumbnail: "/placeholder.svg?height=300&width=400",
      date: "2024-02-28",
      description: "Annual sports festival showcasing student athletic talents",
    },
    {
      id: 7,
      title: "Computer Laboratory",
      category: "facilities",
      type: "image",
      thumbnail: "/placeholder.svg?height=300&width=400",
      date: "2024-01-18",
      description: "Advanced computer lab with latest technology",
    },
    {
      id: 8,
      title: "Cultural Night Performance",
      category: "events",
      type: "image",
      thumbnail: "/placeholder.svg?height=300&width=400",
      date: "2024-03-05",
      description: "Students performing traditional and modern cultural presentations",
    },
    {
      id: 9,
      title: "Campus Aerial View",
      category: "campus",
      type: "image",
      thumbnail: "/placeholder.svg?height=300&width=400",
      date: "2024-01-10",
      description: "Aerial view of the entire SJCSI campus",
    },
  ]

  const categories = [
    { id: "all", label: "All", count: galleryItems.length },
    { id: "events", label: "Events", count: galleryItems.filter((item) => item.category === "events").length },
    {
      id: "facilities",
      label: "Facilities",
      count: galleryItems.filter((item) => item.category === "facilities").length,
    },
    { id: "campus", label: "Campus", count: galleryItems.filter((item) => item.category === "campus").length },
  ]

  const filteredItems =
    selectedCategory === "all" ? galleryItems : galleryItems.filter((item) => item.category === selectedCategory)

  return (
    <div className="min-h-screen py-12">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-gray-900 mb-6">Campus Gallery</h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Explore life at SJCSI through our collection of photos and videos showcasing campus facilities, events, and
            student activities.
          </p>
        </div>

        {/* Category Tabs */}
        <Tabs value={selectedCategory} onValueChange={setSelectedCategory} className="mb-8">
          <TabsList className="grid w-full grid-cols-4 max-w-md mx-auto">
            {categories.map((category) => (
              <TabsTrigger key={category.id} value={category.id} className="text-sm">
                {category.label} ({category.count})
              </TabsTrigger>
            ))}
          </TabsList>
        </Tabs>

        {/* Gallery Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredItems.map((item) => (
            <Dialog key={item.id}>
              <DialogTrigger asChild>
                <Card className="cursor-pointer hover:shadow-lg transition-shadow group">
                  <CardContent className="p-0">
                    <div className="relative overflow-hidden rounded-t-lg">
                      <Image
                        src={item.thumbnail || "./placeholder.svg"}
                        alt={item.title}
                        width={400}
                        height={300}
                        className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                      />
                      <div className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                        {item.type === "video" ? (
                          <Play className="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                        ) : (
                          <ImageIcon className="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                        )}
                      </div>
                      <div className="absolute top-2 right-2">
                        <Badge variant={item.type === "video" ? "destructive" : "secondary"}>
                          {item.type === "video" ? "Video" : "Photo"}
                        </Badge>
                      </div>
                    </div>
                    <div className="p-4">
                      <h3 className="font-semibold text-lg mb-2 group-hover:text-blue-600 transition-colors">
                        {item.title}
                      </h3>
                      <p className="text-gray-600 text-sm mb-3">{item.description}</p>
                      <div className="flex items-center justify-between text-xs text-gray-500">
                        <div className="flex items-center space-x-1">
                          <Calendar className="h-3 w-3" />
                          <span>{item.date}</span>
                        </div>
                        <Badge variant="outline" className="text-xs">
                          {item.category}
                        </Badge>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </DialogTrigger>
              <DialogContent className="max-w-4xl">
                <div className="space-y-4">
                  <div className="relative">
                    {item.type === "video" ? (
                      <div className="aspect-video bg-gray-100 rounded-lg flex items-center justify-center">
                        <div className="text-center">
                          <Play className="h-16 w-16 text-gray-400 mx-auto mb-4" />
                          <p className="text-gray-600">Video player would be embedded here</p>
                        </div>
                      </div>
                    ) : (
                      <Image
                        src={item.thumbnail || "./placeholder.svg"}
                        alt={item.title}
                        width={800}
                        height={600}
                        className="w-full h-auto rounded-lg"
                      />
                    )}
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold mb-2">{item.title}</h2>
                    <p className="text-gray-600 mb-4">{item.description}</p>
                    <div className="flex items-center space-x-4 text-sm text-gray-500">
                      <div className="flex items-center space-x-1">
                        <Calendar className="h-4 w-4" />
                        <span>{item.date}</span>
                      </div>
                      <Badge variant="outline">{item.category}</Badge>
                      <Badge variant={item.type === "video" ? "destructive" : "secondary"}>
                        {item.type === "video" ? "Video" : "Photo"}
                      </Badge>
                    </div>
                  </div>
                </div>
              </DialogContent>
            </Dialog>
          ))}
        </div>

        {/* Load More Button */}
        <div className="text-center mt-12">
          <Button variant="outline" size="lg">
            Load More Items
          </Button>
        </div>

 
      </div>
    </div>
  )
}
