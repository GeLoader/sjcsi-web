"use client"

import { useState, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Calendar, Users, BookOpen, Award, ChevronRight, Bell, ImageIcon } from "lucide-react"
import Link from "next/link"

export default function HomePage() {
  const latestNews = [
    {
      id: 1,
      title: "SJCSI Celebrates Academic Excellence Awards 2024",
      excerpt: "Outstanding students recognized for their exceptional performance across all departments.",
      date: "2024-01-15",
      category: "Academic",
      image: "https://images.unsplash.com/photo-1523050854058-8df90110c9d1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
    },
    {
      id: 2,
      title: "New TESDA Programs Now Available",
      excerpt: "Expanded technical education offerings to meet industry demands.",
      date: "2024-01-12",
      category: "TESDA",
      image: "https://images.unsplash.com/photo-1461749280684-dccba630e2f6?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
    },
    {
      id: 3,
      title: "Campus Infrastructure Improvements Completed",
      excerpt: "Modern facilities enhance learning environment for all students.",
      date: "2024-01-10",
      category: "Campus",
      image: "https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
    },
  ];

  const upcomingEvents = [
    {
      id: 1,
      image: "https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
      title: "Enrollment Period Opens",
      date: "2024-02-01",
      type: "Academic",
    },
    {
      id: 2,
      image: "https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
      title: "Science Fair 2024",
      date: "2024-02-15",
      type: "Event",
    },
    {
      id: 3,
      image: "https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
      title: "Career Guidance Seminar",
      date: "2024-02-20",
      type: "Seminar",
    },
  ]

  const quickStats = [
    { label: "Students Enrolled", value: "2,500+", icon: Users },
    { label: "Faculty Members", value: "150+", icon: Users },
    { label: "Academic Programs", value: "25+", icon: BookOpen },
    { label: "Years of Excellence", value: "30+", icon: Award },
  ]

  const [currentIndex, setCurrentIndex] = useState(0);

  const nextSlide = () => {
    setCurrentIndex((prev) => (prev + 1) % latestNews.length);
  };

  const prevSlide = () => {
    setCurrentIndex((prev) => (prev - 1 + latestNews.length) % latestNews.length);
  };

  // Auto-slide functionality
  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % latestNews.length);
    }, 5000); // Auto-slide every 5 seconds
    
    return () => clearInterval(interval);
  }, [latestNews.length]);

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section
        className="relative text-white py-14"
        style={{
          backgroundImage: `url('./cover-page.png')`,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          backgroundRepeat: 'no-repeat',
          height: '80vh'
        }}
      >
        <div
          className="absolute inset-0 bg-black opacity-20"
          style={{ zIndex: 1 }}
        ></div>
        <div className="relative" style={{ zIndex: 2 }}>
          <img
            className="mx-auto mb-4 w-60 h-60 rounded-full shadow-lg"
            alt="School logo"
            src="/sjcsi-logo.png"
          />
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto text-center">
              <h1 className="text-5xl font-bold mb-6" style={{ color: '#094b3d' }}>
                Welcome to Saint Joseph College of Sindangan Incorporated
              </h1>
            </div>
          </div>
        </div>
      </section>

      {/* Decorative Section */}
      <section 
        className="relative text-white py-14 bg-[#094b3d]"
        style={{
          backgroundImage: `url('./front-section2.png')`,
          backgroundPosition: 'center',
          backgroundRepeat: 'no-repeat',
          height: '10vh'
        }}
      >
      </section>

      {/* Quick Stats */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {quickStats.map((stat, index) => (
              <Card key={index} className="text-center">
                <CardContent className="pt-6">
                  <stat.icon className="h-12 w-12 mx-auto mb-4 text-blue-600" />
                  <h3 className="text-3xl font-bold text-gray-900 mb-2">{stat.value}</h3>
                  <p className="text-gray-600">{stat.label}</p>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>

     {/* Latest News & Announcements */}
     <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="flex items-center justify-between mb-12">
            <div>
              <h2 className="text-3xl font-bold text-[#094b3d] mb-4">Latest News & Announcements</h2>
              <p className="text-gray-600">Stay updated with the latest happenings at SJCSI</p>
            </div>
            <Link
              href="/news"
              className="inline-flex items-center justify-center rounded-md border border-gray-300 bg-transparent px-4 py-2 text-sm font-medium text-[#094b3d] hover:bg-gray-100 focus:outline-none"
            >
              View All News
              <svg className="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
              </svg>
            </Link>
          </div>

          {/* News Carousel */}
          <div className="relative max-w-4xl mx-auto">
            {/* Carousel Container */}
            <div className="overflow-hidden rounded-lg">
              <div 
                className="flex transition-transform duration-500 ease-in-out"
                style={{ transform: `translateX(-${currentIndex * 100}%)` }}
              >
                {latestNews.map((news) => (
                  <div key={news.id} className="w-full flex-shrink-0 px-4">
                    <div className="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow max-w-2xl mx-auto">
                      <img
                        src={news.image}
                        alt={news.title}
                        className="w-full h-76 object-cover rounded-t-lg"
                      />
                      <div className="p-6">
                        <div className="flex items-center justify-between mb-3">
                          <span className="inline-block bg-gray-200 text-gray-700 text-xs font-semibold px-2.5 py-0.5 rounded">
                            {news.category}
                          </span>
                          <span className="text-sm text-gray-500">{news.date}</span>
                        </div>
                        <h3 className="text-xl font-semibold text-[#094b3d] mb-3">{news.title}</h3>
                        <p className="text-gray-600 mb-4 line-clamp-3">{news.excerpt}</p>
                        <Link
                          href={`/news/${news.id}`}
                          className="inline-flex items-center text-[#094b3d] hover:underline font-medium"
                        >
                          Read More
                          <svg className="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                          </svg>
                        </Link>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Navigation Buttons */}
            <button
              onClick={prevSlide}
              className="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-gray text-gray-800 p-2 rounded-full shadow-md transition-all duration-200 z-10"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            
            <button
              onClick={nextSlide}
              className="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow-md transition-all duration-200 z-10"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>

            {/* Dots Indicator */}
            <div className="flex justify-center mt-6 space-x-2">
              {latestNews.map((_, index) => (
                <button
                  key={index}
                  onClick={() => setCurrentIndex(index)}
                  className={`w-3 h-3 rounded-full transition-all duration-200 ${
                    index === currentIndex 
                      ? 'bg-[#094b3d] scale-110' 
                      : 'bg-gray-300 hover:bg-gray-400'
                  }`}
                />
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Upcoming Events */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="max-w-7xl mx-auto">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold text-gray-900 mb-4">Upcoming Events</h2>
              <p className="text-gray-600">Don't miss these important dates and events</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {upcomingEvents.map((event, index) => (
                  <div key={event.id} className="w-full flex-shrink-0 px-4">
                    <div className="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow max-w-2xl mx-auto">
                      <img
                        src={event.image}
                        alt={event.title}
                        className="w-full h-76 object-cover rounded-t-lg"
                      />
                      <div className="p-6">
                        <div className="flex items-center justify-between mb-3">
                         
                          <span className="text-sm text-gray-500 text-center">{event.date}</span>
                        </div>
                        <h3 className="text-xl font-semibold text-[#094b3d] mb-3">{event.title}</h3>
                        
                        <Link
                          href={`/news/${event.id}`}
                          className="inline-flex items-center text-[#094b3d] hover:underline font-medium"
                        >
                          Read More
                          <svg className="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                          </svg>
                        </Link>
                      </div>
                    </div>
                  </div>
                ))}
                 
          
            </div>
            <div className="flex items-center justify-center gap-8">
 
 <Link
   href="/events"
   className="items-center inline-flex items-center justify-center rounded-md border border-gray-300 bg-transparent px-4 py-2 text-sm font-medium text-[#094b3d] hover:bg-gray-100 focus:outline-none gap-8"
 >
   MORE EVENTS
   <svg className="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
     <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
   </svg>
 </Link>
</div>
          </div>
 
        </div>
        
      </section>

 
    </div>
  )
}